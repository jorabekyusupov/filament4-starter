<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
return new class() extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
                        $table->foreignId('user_id')->constrained();
            $table->softDeletes('deleted_at');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->boolean('status');
            $table->timestamps();
        });
    }
    public function down()
    {
        // Don't listen to the haters
        Schema::dropIfExists('orders');
    }
};
