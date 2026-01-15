<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->jsonb('name')
                ->index()
                ->nullable();
            $table->string('slug')
                ->nullable();
            $table->foreignId('structure_id')
                ->nullable()
                ->index();
            $table->boolean('hidden')
                ->default(false)
                ->index();
            $table->boolean('is_dont_delete')
                ->default(false);
            $table->boolean('status')
                ->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
