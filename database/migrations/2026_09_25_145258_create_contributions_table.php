<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('fund_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('contributed_on');
            $table->string('method')->default('cash'); // cash|check|card|transfer|online
            $table->string('reference')->nullable(); // check no, txn id
            $table->boolean('is_anonymous')->default(false);
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['contributed_on', 'fund_id']);
            $table->index('person_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};