<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title',500)->nullable(true);
            $table->string('content',2000);
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('image')->nullable(true);
            $table->enum('type',['feeds','article'])->default('article');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
