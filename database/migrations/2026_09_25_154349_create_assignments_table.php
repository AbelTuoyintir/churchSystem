<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('team_positions')->nullOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->date('assigned_on');
            $table->string('status')->default('scheduled'); // scheduled|confirmed|declined|completed|no_show
            $table->dateTime('reminded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['assigned_on', 'team_id']);
            $table->index(['person_id', 'assigned_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};