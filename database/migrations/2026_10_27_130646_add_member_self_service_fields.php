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
        Schema::table('people', function (Blueprint $table) {
            $table->boolean('directory_opt_in')->default(false)->after('sms_opt_in');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_open')->default(true)->after('is_active');
        });

        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['group_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_join_requests');

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_open');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('directory_opt_in');
        });
    }
};
