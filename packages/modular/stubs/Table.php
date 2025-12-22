<?php

namespace StubModuleNamespace\StubClassNamePrefix\Livewire;

use App\View\Components\Livewire\CustomComponent;

use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use StubModuleNamespace\StubClassNamePrefix\Models\ModelName;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;


class StubClassNamePrefixTable extends CustomComponent
{

    public function table(Table $table): Table
    {

        return $table
            ->query(ModelName::query())
            ->modelLabel(__('TableLable'))
            ->pluralModelLabel(__('TableLable'))
            ->description(__('TableLable'))
            ->columns($this->columns())
            ->actions([
                ViewAction::make()
                    ->model(ModelName::class)
                    ->hidden(!$this->viewPermission)
                    ->iconButton()
                    ->form($this->schema()),
                EditAction::make()
                    ->model(ModelName::class)
                    ->iconButton()
                    ->hidden(!$this->editPermission)
                    ->form($this->schema()),
                DeleteAction::make()
                    ->model(ModelName::class)
                    ->iconButton()
                    ->hidden(!$this->deletePermission)
                    ->sendSuccessNotification()
            ])
            ->headerActions([
                CreateAction::make()
                    ->model(ModelName::class)
                    ->label(__('create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(!$this->createPermission)
                    ->form($this->schema()),
            ]);

    }

    public function schema(): array
    {
        return [
            SCHEMA_REPLACE
        ];
    }

    public function columns(): array
    {
        return [
            COLUMNS_REPLACE
        ];
    }
}