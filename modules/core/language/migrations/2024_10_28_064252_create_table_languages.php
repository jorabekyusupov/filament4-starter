<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('code')->index();
            $table->string('flag')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_default')->default(0)->index();
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
        Artisan::call("insert-default-lang");
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
