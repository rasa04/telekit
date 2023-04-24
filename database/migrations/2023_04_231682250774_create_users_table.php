<?php

use Core\Database\Blueprint;
use Core\Database\Migration;
use Core\Database\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->string("role");
            $table->json("context");
        });
    }

    public function down(): void
    {
        Schema::dropIfExist('users');
    }
};

