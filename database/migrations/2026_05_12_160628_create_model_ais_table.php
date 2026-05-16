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
        Schema::create('model_ais', function (Blueprint $table) {
            $table->id();
            $table->enum('models',['deep fake','face recognation','face reconstruction','dna model']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_ais');
    }
};
