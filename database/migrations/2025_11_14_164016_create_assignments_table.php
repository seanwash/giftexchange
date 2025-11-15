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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('giver_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('participants')->cascadeOnDelete();
            $table->timestamps();

            // Ensure each participant can only have one assignment per event
            $table->unique(['event_id', 'giver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
