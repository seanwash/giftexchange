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
        Schema::rename('participant_likes', 'participant_interests');

        Schema::table('participant_interests', function (Blueprint $table) {
            $table->renameColumn('like_text', 'interest_text');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('has_entered_likes', 'has_entered_interests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('has_entered_interests', 'has_entered_likes');
        });

        Schema::table('participant_interests', function (Blueprint $table) {
            $table->renameColumn('interest_text', 'like_text');
        });

        Schema::rename('participant_interests', 'participant_likes');
    }
};
