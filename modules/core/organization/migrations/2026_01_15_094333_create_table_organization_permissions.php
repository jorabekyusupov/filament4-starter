<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organization_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('permission_id');
            $table->unique(['organization_id', 'permission_id'], 'org_perm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_permissions');
    }
};
