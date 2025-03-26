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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('conference_id')->constrained('all_conferences')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('conference_roles')->onDelete('cascade');
            $table->string('Name');
            $table->string('Email')->nullable();
            $table->string('Number')->nullable();
            $table->string('Sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
