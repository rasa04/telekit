<?php

use Core\Database\Blueprint;
use Core\Database\Migration;
use Core\Database\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->integer("group_id");
            $table->string("rights");
        });
    }

    public function down(): void
    {
        Schema::dropIfExist('groups');
    }
};

