<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workspace_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('permission_id');
            $table->unique(['workspace_id', 'permission_id'], 'workspace_perm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_permissions');
    }
};
