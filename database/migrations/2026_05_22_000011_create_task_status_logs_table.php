<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('old_status_id')->nullable()->constrained('task_statuses')->nullOnDelete();
            $table->foreignId('new_status_id')->nullable()->constrained('task_statuses')->nullOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_logs');
    }
};
