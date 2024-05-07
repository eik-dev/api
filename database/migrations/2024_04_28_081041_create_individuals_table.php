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
            $table->foreignId('userID')->constrained('users');
            $table->string('category');
            $table->string('username');
            $table->string('firm')->nullable();
            $table->string('alternate')->nullable();
            $table->string('nationality')->nullable();
            $table->integer('nationalID');
            $table->string('postal')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('nema')->nullable();
            $table->string('kra')->nullable();
            $table->integer('phone')->nullable();
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
