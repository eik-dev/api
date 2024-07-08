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
            $table->date('StartDate');
            $table->date('EndDate')->nullable();
            $table->string('View')->default('certificates.training');
            $table->string('Background')->default('system/training.jpg');
            $table->string('Style')->nullable();
            $table->string('Info');
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
