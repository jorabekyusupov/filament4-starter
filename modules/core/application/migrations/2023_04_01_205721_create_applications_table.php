<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique()->index();
            $table->string('password');
            $table->string('webhook_url')->nullable();
            $table->text('secret_private_key')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
