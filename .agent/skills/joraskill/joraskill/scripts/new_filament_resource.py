#!/usr/bin/env python3
"""Generate Filament Resource scaffolding in Applications-style structure."""

from __future__ import annotations

import argparse
import re
from pathlib import Path

def find_repo_root(start: Path) -> Path:
    current = start.resolve()
    for _ in range(6):
        if (current / "composer.json").exists():
            return current
        current = current.parent
    raise SystemExit("Could not locate repo root (composer.json not found).")


REPO_ROOT = find_repo_root(Path(__file__).parent)


def studly(name: str) -> str:
    parts = re.split(r"[^a-zA-Z0-9]+", name.strip())
    return "".join(p.capitalize() for p in parts if p)


def pluralize(name: str) -> str:
    if name.endswith("y") and name[-2:].lower() not in ("ay", "ey", "iy", "oy", "uy"):
        return name[:-1] + "ies"
    if name.endswith("s"):
        return name + "es"
    return name + "s"


def main() -> None:
    parser = argparse.ArgumentParser(description="Create Filament Resource structure in a module.")
    parser.add_argument("module", help="Module name (folder under modules/core), e.g. application")
    parser.add_argument("resource", help="Resource name singular, e.g. Application")
    parser.add_argument("--plural", help="Resource name plural, e.g. Applications", default=None)
    parser.add_argument("--model", help="Model class, e.g. Modules\\Application\\Models\\Application", default=None)
    args = parser.parse_args()

    module = args.module.strip()
    resource_name = studly(args.resource)
    plural = studly(args.plural) if args.plural else studly(pluralize(resource_name))
    resource_slug = plural.lower()
    module_ns = studly(module)
    model_class = args.model or f"Modules\\{module_ns}\\Models\\{resource_name}"

    base_dir = REPO_ROOT / "modules" / "core" / module / "src" / "Filament" / "Resources" / plural
    pages_dir = base_dir / "Pages"
    schemas_dir = base_dir / "Schemas"
    tables_dir = base_dir / "Tables"

    pages_dir.mkdir(parents=True, exist_ok=True)
    schemas_dir.mkdir(parents=True, exist_ok=True)
    tables_dir.mkdir(parents=True, exist_ok=True)

    resource_file = base_dir / f"{resource_name}Resource.php"
    list_page = pages_dir / f"List{plural}.php"
    create_page = pages_dir / f"Create{resource_name}.php"
    edit_page = pages_dir / f"Edit{resource_name}.php"
    form_file = schemas_dir / f"{resource_name}Form.php"
    info_file = schemas_dir / f"{resource_name}Infolist.php"
    table_file = tables_dir / f"{plural}Table.php"

    if resource_file.exists():
        raise SystemExit("Resource already exists.")

    resource_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural};

use BackedEnum;
use Filament\\Resources\\Resource;
use Filament\\Schemas\\Schema;
use Filament\\Support\\Icons\\Heroicon;
use Filament\\Tables\\Table;
use Illuminate\\Contracts\\Support\\Htmlable;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Schemas\\{resource_name}Form;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Schemas\\{resource_name}Infolist;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Tables\\{plural}Table;
use {model_class};
use UnitEnum;

class {resource_name}Resource extends Resource
{{
    protected static ?string $model = {resource_name}::class;

    protected static ?string $slug = '{resource_slug}';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {{
        return 'heroicon-o-rectangle-stack';
    }}

    public static function getLabel(): ?string
    {{
        return __('{resource_name.lower()}');
    }}

    public static function getPluralLabel(): ?string
    {{
        return __('{resource_slug}');
    }}

    public static function getNavigationLabel(): string
    {{
        return __('{resource_slug}');
    }}

    public static function getNavigationGroup(): string|UnitEnum|null
    {{
        return __('settings');
    }}

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {{
        return {resource_name}Form::configure($schema);
    }}

    public static function infolist(Schema $schema): Schema
    {{
        return {resource_name}Infolist::configure($schema);
    }}

    public static function table(Table $table): Table
    {{
        return {plural}Table::table($table);
    }}

    public static function getPages(): array
    {{
        return [
            'index' => Pages\\List{plural}::route('/'),
            'create' => Pages\\Create{resource_name}::route('/create'),
            'edit' => Pages\\Edit{resource_name}::route('/{{record}}/edit'),
        ];
    }}
}}
"""

    page_list_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Pages;

use Filament\\Actions\\CreateAction;
use Filament\\Resources\\Pages\\ListRecords;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\{resource_name}Resource;

class List{plural} extends ListRecords
{{
    protected static string $resource = {resource_name}Resource::class;

    protected function getHeaderActions(): array
    {{
        return [
            CreateAction::make(),
        ];
    }}
}}
"""

    page_create_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Pages;

use Filament\\Resources\\Pages\\CreateRecord;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\{resource_name}Resource;

class Create{resource_name} extends CreateRecord
{{
    protected static string $resource = {resource_name}Resource::class;
}}
"""

    page_edit_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Pages;

use Filament\\Actions\\DeleteAction;
use Filament\\Resources\\Pages\\EditRecord;
use Modules\\{module_ns}\\Filament\\Resources\\{plural}\\{resource_name}Resource;

class Edit{resource_name} extends EditRecord
{{
    protected static string $resource = {resource_name}Resource::class;

    protected function getHeaderActions(): array
    {{
        return [
            DeleteAction::make(),
        ];
    }}
}}
"""

    form_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Schemas;

use Filament\\Schemas\\Schema;
use Filament\\Schemas\\Components\\Section;
use Filament\\Forms\\Components\\TextInput;

class {resource_name}Form
{{
    public static function configure(Schema $schema): Schema
    {{
        return $schema
            ->components([
                Section::make(__('General Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }}
}}
"""

    info_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Schemas;

use Filament\\Schemas\\Schema;
use Filament\\Schemas\\Components\\Section;
use Filament\\Infolists\\Components\\TextEntry;

class {resource_name}Infolist
{{
    public static function configure(Schema $schema): Schema
    {{
        return $schema
            ->components([
                Section::make(__('General Information'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Name')),
                    ])->columns(2),
            ]);
    }}
}}
"""

    table_php = f"""<?php

namespace Modules\\{module_ns}\\Filament\\Resources\\{plural}\\Tables;

use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Table;

class {plural}Table
{{
    public static function table(Table $table): Table
    {{
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
            ]);
    }}
}}
"""

    resource_file.write_text(resource_php, encoding="utf-8")
    list_page.write_text(page_list_php, encoding="utf-8")
    create_page.write_text(page_create_php, encoding="utf-8")
    edit_page.write_text(page_edit_php, encoding="utf-8")
    form_file.write_text(form_php, encoding="utf-8")
    info_file.write_text(info_php, encoding="utf-8")
    table_file.write_text(table_php, encoding="utf-8")

    print(f"Created: {resource_file}")
    print(f"Created: {list_page}")
    print(f"Created: {create_page}")
    print(f"Created: {edit_page}")
    print(f"Created: {form_file}")
    print(f"Created: {info_file}")
    print(f"Created: {table_file}")


if __name__ == "__main__":
    main()
