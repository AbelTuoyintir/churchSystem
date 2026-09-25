<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('channel'); // email|sms
            $table->string('address'); // resolved email or phone at send time
            $table->string('status')->default('pending'); // pending|sent|delivered|failed|bounced
            $table->dateTime('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['message_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};