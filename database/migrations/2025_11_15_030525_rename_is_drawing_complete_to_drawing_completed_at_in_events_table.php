<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('drawing_completed_at')->nullable()->after('theme');
        });

        // Migrate existing data: if is_drawing_complete is true, set drawing_completed_at to created_at
        DB::table('events')
            ->where('is_drawing_complete', true)
            ->update(['drawing_completed_at' => DB::raw('created_at')]);

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_drawing_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_drawing_complete')->default(false)->after('theme');
        });

        // Migrate existing data: if drawing_completed_at is not null, set is_drawing_complete to true
        DB::table('events')
            ->whereNotNull('drawing_completed_at')
            ->update(['is_drawing_complete' => true]);

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('drawing_completed_at');
        });
    }
};
