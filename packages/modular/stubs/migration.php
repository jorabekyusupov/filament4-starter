<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class() extends Migration {
    public function up()
    {
        Schema::create('StubModuleName', function (Blueprint $table) {
            $table->id();
            //columns
            $table->timestamps();
        });
    }

    public function down()
    {
        // Don't listen to the haters
        Schema::dropIfExists('StubModuleName');
    }
};
