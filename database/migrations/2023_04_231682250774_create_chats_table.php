<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return new class extends Migration
{
    public function up(): void
    {
        Capsule::schema()->create('chats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id');
            $table->string('first_name')->nullable();
            $table->string('username')->nullable();
            $table->string('language')->nullable();
            $table->enum('rights', [0, 1, 2])->default(0);
            $table->json('context')->default('[]');
            $table->enum('type', ['private', 'group', 'supergroup']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('chats');
    }
};

