<?php

use Core\Database\Blueprint;
use Core\Database\Migration;
use Core\Database\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("name");
        });
    }

    public function down(): void
    {
        Schema::dropIfExist('countries');
    }
};

