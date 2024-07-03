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
        Schema::create('all_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->date('Date');
            $table->string('View');
            $table->string('Background');
            $table->string('Style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_trainings');
    }
};
