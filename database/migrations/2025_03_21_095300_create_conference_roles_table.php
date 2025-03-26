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
        Schema::create('conference_roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('conference_id')->constrained('all_conferences')->onDelete('cascade');
            $table->string('Name');
            $table->string('Background')->nullable();
            $table->string('Style')->nullable();
            $table->string('Info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_roles');
    }
};
