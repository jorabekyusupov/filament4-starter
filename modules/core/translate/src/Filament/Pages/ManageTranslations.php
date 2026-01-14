<?php

declare(strict_types=1);

namespace Modules\Translate\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;

use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
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
    use InteractsWithTable {
        table as protected baseTable;
    }
    use HasPageShield;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-language';

    protected  string $view = 'translate::filament.pages.manage-translations';

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

    public function table(Table $table): Table
    {
        return $this->baseTable($table)
            ->groups([
                Group::make('group')
                    ->label(__('group'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn (TranslationEntry $record): string => __('source') . ': ' . __($record->getAttribute('group'))
                    )
                    ->getDescriptionFromRecordUsing(
                        fn (TranslationEntry $record): string => __(':count translations', [
                            'count' => (int) $record->getAttribute('group_count'),
                        ])
                    ),
            ])
            ->defaultGroup('group')
            ->filtersLayout(FiltersLayout::AboveContent);
    }

    protected function getTableQuery(): Builder
    {
        return TranslationEntry::query();
    }

    protected function getTableColumns(): array
    {
        $columns = [
            TextColumn::make('group')
                ->label(__('group'))
                ->badge()
                ->formatStateUsing(fn (?string $state): string => __('source') . ': ' . __($state ?? ''))
                ->color(fn (?string $state): string => $state === TranslationService::MAIN_GROUP ? 'gray' : 'primary')
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable()
                ->searchable(),
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
                        $record->getAttribute('group'),
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
                                ->rule(fn () => Rule::notIn($this->getExistingKeys(TranslationService::MAIN_GROUP)))
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
                        TranslationService::MAIN_GROUP,
                    );

                    $this->resetTable();
                }),
            Action::make('statistics')
                ->label(__('statistics'))
                ->icon('heroicon-o-chart-bar-square')
                ->modalWidth(Width::ScreenExtraLarge)
                ->modalContent(fn () => view('translate::filament.pages.modals.statistics', [
                    'statistics' => $this->translationService()->getStatistics(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('close')),
            Action::make('download')
                ->label(__('download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->modalWidth(Width::SixExtraLarge)
                ->form(function () {
                    $counts = $this->translationService()->getGroupCounts();
                    $options = collect($counts)->map(fn ($count, $group) => __($group))->all();
                    $descriptions = collect($counts)->map(fn ($count) => $count . ' ' . str('string')->plural($count))->all();

                    return [
                        Section::make(__('Select Modules'))
                            ->description(__('Choose the modules you want to export. The zip file will be structured by module.'))
                            ->icon('heroicon-o-square-3-stack-3d')
                            ->schema([
                                CheckboxList::make('groups')
                                    ->hiddenLabel()
                                    ->options($options)
                                    ->descriptions($descriptions)
                                    ->default(array_keys($options))
                                    ->columns(4)
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->required(),
                            ]),
                    ];
                })
                ->action(fn (array $data): BinaryFileResponse => $this->translationService()->createZip($data['groups'])),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('group')
                ->label(__('group'))
                ->options($this->getGroupOptions())
                ->placeholder(__('all')),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function getTableRecords(): EloquentCollection|Paginator|CursorPaginator
    {
        $rows = collect($this->translationService()->getTableRows());
        $rows = $this->applyFiltersToRows($rows);
        $rows = $this->applySearchToRows($rows);
        $rows = $this->applyOrderingToRows($rows);

        $groupCounts = $rows
            ->groupBy('group')
            ->map(fn (Collection $items): int => $items->count());

        $rows = $rows->map(function (array $row) use ($groupCounts): array {
            $row['group_count'] = $groupCounts[$row['group']] ?? 0;

            return $row;
        });

        $records = $rows
            ->map(fn (array $row): TranslationEntry => TranslationEntry::fromArray($row))
            ->values();

        return $this->cachedTableRecords = new EloquentCollection($records);
    }

    protected function applyFiltersToRows(Collection $rows): Collection
    {
        $groupFilter = $this->getTableFilterState('group')['value'] ?? null;

        if (blank($groupFilter)) {
            return $rows;
        }

        return $rows->where('group', $groupFilter)->values();
    }

    public function getTableRecord(?string $key): ?Model
    {
        if ($this->cachedTableRecords === null) {
            $this->getTableRecords();
        }

        return $this->cachedTableRecords?->first(function (Model $record) use ($key): bool {
            return (string) $record->getKey() === (string) $key;
        });
    }

    public function getTableRecordKey(Model|array $record): string
    {
        if ($record instanceof Model) {
            return (string) $record->getKey();
        }

        return (string) data_get($record, 'id', data_get($record, 'key'));
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

            if (isset($row['group']) && Str::contains(Str::lower($row['group']), $needle)) {
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

    protected function applyOrderingToRows(Collection $rows): Collection
    {
        $grouping = $this->getTableGrouping();
        $groupColumn = $grouping?->getColumn() ?? 'group';
        $groupDirection = $this->getTableGroupingDirection() ?? 'asc';
        $sortColumn = $this->getTableSortColumn();
        $sortDirection = $this->getTableSortDirection() ?? 'asc';
        $groupCounts = $rows
            ->groupBy($groupColumn)
            ->map(fn (Collection $items): int => $items->count());

        return $rows
            ->sort(function (array $a, array $b) use ($groupColumn, $groupDirection, $groupCounts, $sortColumn, $sortDirection): int {
                $countComparison = $this->compareNumbers(
                    $groupCounts[$a[$groupColumn] ?? null] ?? 0,
                    $groupCounts[$b[$groupColumn] ?? null] ?? 0,
                    $groupDirection
                );

                if ($countComparison !== 0) {
                    return $countComparison;
                }

                $groupComparison = $this->compareValues($a[$groupColumn] ?? null, $b[$groupColumn] ?? null, 'asc');

                if ($groupComparison !== 0) {
                    return $groupComparison;
                }

                if ($sortColumn) {
                    $columnComparison = $this->compareValues($a[$sortColumn] ?? null, $b[$sortColumn] ?? null, $sortDirection);

                    if ($columnComparison !== 0) {
                        return $columnComparison;
                    }
                }

                return $this->compareValues($a['key'] ?? null, $b['key'] ?? null, 'asc');
            })
            ->values();
    }

    private function compareValues(mixed $left, mixed $right, string $direction): int
    {
        $left = $left ?? '';
        $right = $right ?? '';

        $result = strnatcasecmp((string) $left, (string) $right);

        return $direction === 'desc' ? -$result : $result;
    }

    private function compareNumbers(int|float $left, int|float $right, string $direction): int
    {
        $result = $left <=> $right;

        return $direction === 'desc' ? -$result : $result;
    }

    protected function getExistingKeys(?string $group = null): array
    {
        return collect($this->translationService()->getTableRows())
            ->when($group, fn (Collection $rows) => $rows->where('group', $group))
            ->pluck('key')
            ->unique()
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

    private function getGroupOptions(): array
    {
        return collect($this->translationService()->getTableRows())
            ->pluck('group')
            ->unique()
            ->sort()
            ->mapWithKeys(fn (string $group): array => [$group => __($group)])
            ->all();
    }
}
