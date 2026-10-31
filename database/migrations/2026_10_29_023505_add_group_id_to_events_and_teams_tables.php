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
        Schema::table('events', function (Blueprint $table): void {
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('teams', function (Blueprint $table): void {
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('group_id');
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('group_id');
        });
    }
};
