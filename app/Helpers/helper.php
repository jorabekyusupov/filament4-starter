<?php

use Illuminate\Database\Eloquent\Builder;

function getLocales(): array
{

    return app(\Modules\Language\Repositories\LanguageReadRepositoryInterface::class)
        ->getActiveLanguages()
        ->pluck('code')
        ->toArray();

}

function getLocaleLabels(): array
{
    return app(\Modules\Language\Repositories\LanguageReadRepositoryInterface::class)
        ->getActiveLanguages()
        ->pluck('name', 'code')
        ->toArray();

}

function getNumberLocale(?string $locale = null): string
{
    $locale ??= app()->getLocale();

    $mappedLocale = [
        // `oz` (Latin Uzbek) is not recognised by PHP's intl NumberFormatter,
        // so map it to the ICU-compatible locale code.
        'oz' => 'uz_Latn',
    ][$locale] ?? $locale;

    try {
        new \NumberFormatter($mappedLocale, \NumberFormatter::DECIMAL);

        return $mappedLocale;
    } catch (\Throwable) {
        $fallback = config('app.fallback_locale', 'en');

        return $fallback !== $mappedLocale ? getNumberLocale($fallback) : 'en';
    }
}

function filterNullAndEmpty(array $data): array
{
    return array_filter($data, static function ($value, $key) {
        return $value !== null && $value !== "";
    }, ARRAY_FILTER_USE_BOTH);
}

function module_path($module): string
{
    return base_path('modules/' . $module);
}

//function getTransForms($nameInput = 'name', $description = false): array
//{
//    return [
//        \SolutionForest\FilamentTranslateField\Forms\Component\Translate::make()
//            ->schema(array_merge(
//                [
//                    \Filament\Forms\Components\TextInput::make($nameInput)
//                        ->label(__('name'))
//                        ->required()
//                        ->maxLength(255),
//
//                ], $description ? [
//                \Filament\Forms\Components\Textarea::make('description')
//                    ->label(__('description'))
//                    ->maxLength(65535),
//            ] : []
//            ))
//            ->locales(getLocales())
//            ->localeLabels(getLocaleLabels())
//            ->columnSpanFull(),
//    ];
//
//}

function getWhereTranslationColumns(Builder $query, $name = 'name', $search = null): Builder
{
    $languages = app(\Modules\Language\Repositories\LanguageReadRepositoryInterface::class)->getActiveLanguages();
    $query->where(function ($query) use ($languages, $name, $search) {
        $query->when($search, function ($query) use ($languages, $name, $search) {
            foreach ($languages as $language) {
                $code = $language->code;
                if ($language->is_default) {
                    $query->orWhere($name . '->' . $code, 'ilike', '%' . $search . '%');
                    continue;
                }
                $query->orWhere($name . '->' . $code, 'like', '%' . $search . '%');
            }
        });
    });

    return $query;
}

function getChildLocationOrganizations(): array
{
    return array_merge(cache()->get('user_' . auth()->id() . '_second_parent_organization', []), [auth()->user()->organization_id]);
}

function getChildMainOrganizations(): array
{
    return array_merge(cache()->get('user_' . auth()->id() . '_first_parent_organization', []), [auth()->user()->organization_id]);
}
if (!function_exists('get_intl_locale')) {
    function get_intl_locale()
    {
        $currentLocale = app()->getLocale();

        // Agar locale "oz" bo'lsa, uni standart "uz_Latn" ga o'giramiz
        if ($currentLocale === 'oz') {
            return 'uz_Latn';
        }

        // Qolgan holatlarda o'zini qaytaramiz (ru, en va h.k.)
        return $currentLocale;
    }
}


function parsePinfl(string $pinfl): array
{
    // faqat raqamlarni qoldiramiz
    $pinfl = preg_replace('/\D/', '', $pinfl);

    // 14 xonali bo‘lishi kerak
    if (strlen($pinfl) !== 14) {
        throw new InvalidArgumentException("PINFL 14 xonali bo‘lishi kerak");
    }

    // bo‘lib olamiz
    $index = (int)substr($pinfl, 0, 1);   // 1-raqam
    $birthRaw = substr($pinfl, 1, 6);         // DDMMYY
    $regionCode = substr($pinfl, 7, 3);         // 8-10
    $orderNumber = substr($pinfl, 10, 3);        // 11-13
    $controlDigit = (int)substr($pinfl, 13, 1);  // 14

    // indeks 1–8 oralig‘ida bo‘lishi kerak
    if ($index < 1 || $index > 8) {
        throw new InvalidArgumentException("PINFL indeksi 1 dan 8 gacha bo‘lishi kerak.");
    }

    // jins: birinchi son toq → erkak, juft → ayol
    $gender = ($index % 2 === 1) ? 'M' : 'F';
    $genderText = $gender === 'M' ? 'male' : 'famale';

    // tug‘ilgan sana elementlari
    $day = (int)substr($birthRaw, 0, 2);
    $month = (int)substr($birthRaw, 2, 2);
    $year = (int)substr($birthRaw, 4, 2);
    $centuryStart = 1800 + (int)floor(($index - 1) / 2) * 100;
    $fullYear = $centuryStart + $year;

    // ISO ko‘rinishdagi sana
    $birthIso = sprintf('%04d-%02d-%02d', $fullYear, $month, $day);

    // xohlasangiz bu yerda sana validligini ham tekshirishingiz mumkin:
    // if (!checkdate($month, $day, $fullYear)) { ... }

    return [
        'raw' => $pinfl,

        'index' => [
            'value' => $index,
            'century_start' => $centuryStart,        // 1800, 1900, 2000, 2100 ...
        ],

        'gender' => [
            'value' => $gender,                      // 'M' yoki 'F'
            'text' => $genderText,                  // 'erkak' / 'ayol'
        ],

        'birth_date' => [
            'raw' => $birthRaw,                    // "DDMMYY"
            'day' => $day,
            'month' => $month,
            'year' => $fullYear,                    // masalan 1980
            'iso' => $birthIso,                    // "1980-01-01"
        ],

        'region' => [
            'code' => $regionCode,
        ],

        'order_number' => $orderNumber,
        'control_digit' => $controlDigit,
    ];
}

function getNameInputsFilament($name = 'name', $required = true, $description = false, $descriptionName = 'description', $descriptionRequired = false): array
{
    $languages = app(\Modules\Language\Repositories\LanguageReadRepositoryInterface::class)->getActiveLanguages();

    $tabs = [];


    foreach ($languages as $language) {
        $tabs[] = \Filament\Schemas\Components\Tabs\Tab::make(strtoupper($language->code))
            ->label($language->name)
            ->schema(array_filter([
                \Filament\Forms\Components\TextInput::make($name . '.' . $language->code)
                    ->label(__($name . '_' . $language->code))
                    ->required($required),
                $description ? \Filament\Forms\Components\Textarea::make($descriptionName . '.' . $language->code)
                    ->label(__($descriptionName . '_' . $language->code))
                    ->required($descriptionRequired) : null,
            ]));
    }

    return [
        \Filament\Schemas\Components\Tabs::make($name . '_translations')
            ->tabs($tabs)
            ->columnSpanFull()
    ];
}
