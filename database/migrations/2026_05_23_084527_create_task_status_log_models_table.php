<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_task_status_log', function (Blueprint $table) {
            $table->id('status_log_id');
            $table->integer('task_id'); // Yellow FK
            $table->integer('assigned_by')->nullable(); // Yellow FK
            $table->integer('from_status_id'); // Yellow FK
            $table->integer('to_status_id'); // Yellow FK
            $table->integer('changed_by'); // Yellow FK
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
            $table->integer('department_id'); // Yellow FK
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_task_status_log');
    }
};