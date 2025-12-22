<?php

namespace Modules\Translate\Filament\Pages;


use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Translate\Models\TranslationEntry;
use Modules\Translate\Services\TranslationService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManageTranslations extends Page implements HasTable
{
    use InteractsWithTable,HasPageShield;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-language';

    protected string $view = 'translate::filament.pages.manage-translations';

    public function getTitle(): string|Htmlable
    {
        return __('translates');
    }

    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('settings');
    }

    /**
     * @return string|null
     */
    public static function getNavigationLabel(): string
    {
        return __('translates');
    }

    public array $locales = [];

    public function mount(): void
    {
        $this->locales = getLocales();

        if ($this->locales === []) {
            $this->locales = [config('app.locale')];
        }
    }

    protected function getTableQuery(): Builder
    {
        return TranslationEntry::query();
    }

    protected function getTableColumns(): array
    {
        $columns = [
            TextColumn::make('key')
                ->label(__('key'))
                ->searchable()
                ->wrap(),
        ];

        foreach ($this->locales as $locale) {
            $columns[] = TextInputColumn::make($locale)
                ->label(__(strtolower('locale_' . $locale)))
                ->updateStateUsing(function (Model $record, ?string $state) use ($locale) {
                    $this->translationService()->updateTranslation(
                        $locale,
                        $record->getAttribute('key'),
                        $state,
                    );

                    $record->setAttribute($locale, $state ?? null);
                    $this->flushCachedTableRecords();

                    return $state;
                });
        }

        return $columns;
    }

    protected function getTableHeaderActions(): array
    {
        $targetLocale = $this->resolveTargetLocale();

        return [
            Action::make('createKey')
                ->label(__('create-key'))
                ->icon('heroicon-o-plus')
                ->form([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('key')
                                ->label(__('key'))
                                ->required()
                                ->maxLength(255)
                                ->rule(fn () => Rule::notIn($this->getExistingKeys()))
                                ->validationMessages([
                                    'not_in' => __('This key already exists.'),
                                ]),
                            TextInput::make('value')
                                ->label(__(strtolower('locale_' . $targetLocale)))
                                ->maxLength(65535),
                        ])
                ])
                ->action(function (array $data) use ($targetLocale) {
                    $this->translationService()->updateTranslation(
                        $targetLocale,
                        $data['key'],
                        $data['value'] ?? '',
                    );

                    $this->resetTable();
                }),
            Action::make('download')
                ->label(__('download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->action(fn (): BinaryFileResponse => $this->translationService()->createZip()),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function getTableRecords(): EloquentCollection|Paginator|CursorPaginator
    {
        $rows = collect($this->translationService()->getTableRows());
        $rows = $this->applySearchToRows($rows);

        $records = $rows
            ->map(fn (array $row): TranslationEntry => TranslationEntry::fromArray($row))
            ->values();

        return $this->cachedTableRecords = new EloquentCollection($records);
    }

    public function getTableRecord(?string $key): Model|array|null
    {
        if ($this->cachedTableRecords === null) {
            $this->getTableRecords();
        }

        return $this->cachedTableRecords?->first(
            fn (Model $record): bool => (string) $record->getAttribute('key') === (string) $key
        );
    }

    public function getTableRecordKey(Model|array $record): string
    {
        return (string) $record->getAttribute('key');
    }

    public function getAllTableRecordsCount(): int
    {
        return $this->getTableRecords()->count();
    }

    protected function applySearchToRows(Collection $rows): Collection
    {
        $search = $this->getTableSearch();

        if (blank($search)) {
            return $rows;
        }

        $needle = Str::lower($search);

        return $rows->filter(function (array $row) use ($needle): bool {
            if (Str::contains(Str::lower($row['key']), $needle)) {
                return true;
            }

            foreach ($this->locales as $locale) {
                $value = $row[$locale] ?? null;

                if (is_string($value) && Str::contains(Str::lower($value), $needle)) {
                    return true;
                }
            }

            return false;
        });
    }

    protected function getExistingKeys(): array
    {
        return collect($this->translationService()->getTableRows())
            ->pluck('key')
            ->all();
    }

    protected function resolveTargetLocale(): string
    {
        $fallback = config('app.fallback_locale') ?? config('app.locale');

        if ($fallback && in_array($fallback, $this->locales, true)) {
            return $fallback;
        }

        return $this->locales[0] ?? $fallback ?? 'en';
    }

    private function translationService(): TranslationService
    {
        return app(TranslationService::class);
    }
}
