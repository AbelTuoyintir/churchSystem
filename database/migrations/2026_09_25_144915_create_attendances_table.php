<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // person_id nullable so you can record anonymous headcounts
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attended_on');
            $table->string('status')->default('present'); // present|absent|excused|late
            $table->dateTime('checked_in_at')->nullable();
            $table->unsignedInteger('headcount')->nullable(); // for anonymous counts
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['attended_on', 'service_id']);
            $table->index(['person_id', 'attended_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};