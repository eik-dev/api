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
        Schema::create('all_conferences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('Name');
            $table->date('StartDate');
            $table->date('EndDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_conferences');
    }
};
