<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('scope')->default('public'); // public|group|pastoral
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('status')->default('open'); // open|praying|answered
            $table->timestamps();

            $table->index(['scope', 'created_at']);
            $table->index(['person_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
