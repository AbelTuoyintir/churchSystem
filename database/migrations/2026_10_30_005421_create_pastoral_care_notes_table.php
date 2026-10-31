<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pastoral_care_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 32)->default('note'); // note|visit|followup|counselling
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('status', 32)->default('open'); // open|in_progress|completed|archived
            $table->string('priority', 16)->default('normal'); // low|normal|high
            $table->date('due_date')->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pastoral_care_notes');
    }
};
