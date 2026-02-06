<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('namespace');
            $table->text('description')->nullable();
            $table->string('alias')->nullable();
            $table->string('path');
            $table->string('source')->index()->default('admin');
            $table->string("confidentiality")->index()
                ->default("internal");
            $table->boolean('stable')->index()->default(true);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('status')->index()->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('module_tables', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('name');
            $table->boolean('soft_deletes')->default(false);
            $table->boolean('timestamps')->default(true);
            $table->boolean('status')->default(true);
            $table->boolean('logged')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('module_table_columns', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('module_table_id');
            $table->string('name');
            $table->string('type')->default('string');
            $table->text('options')->nullable();
            $table->boolean('nullable')->default(false);
            $table->boolean('unique')->default(false);
            $table->boolean('index')->default(false);
            $table->boolean('foreign')->default(false);
            $table->string('foreign_table')->nullable();
            $table->string('foreign_column')->nullable();
            $table->string('on_delete')->nullable();
            $table->string('on_update')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
