<?php

namespace Modules\Language\Seeders;

use Illuminate\Database\Seeder;
use Modules\Language\Models\Language;

class DefaultLangSeeder extends Seeder
{
    public function run(): void
    {
        $langs = [
            [
                'name' => 'English',
                'code' => 'en',
                'is_default' => 0,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'O\'zbekcha',
                'code' => 'oz',
                'is_default' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Русский',
                'code' => 'ru',
                'is_default' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ўзбекча',
                'code' => 'uz',
                'is_default' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Language::truncate();
        Language::query()->insert($langs);

    }
}
