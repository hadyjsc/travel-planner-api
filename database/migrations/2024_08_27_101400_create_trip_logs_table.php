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
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips', 'id');
            $table->text('original_data');
            $table->tinyInteger('action');
            $table->foreignId('action_by')->constrained('users', 'id');
            $table->timestamp('action_at');
            $table->index(['trip_id', 'action_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
