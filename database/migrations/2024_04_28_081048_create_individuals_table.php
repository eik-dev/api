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
        Schema::create('individuals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users');
            $table->string('firm')->nullable();
            $table->string('category');
            $table->string('alternate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('nationalID')->unique();
            $table->string('postal')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('phone')->nullable();
            $table->longText('bio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individuals');
    }
};
