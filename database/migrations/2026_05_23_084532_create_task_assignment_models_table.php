<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_task_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->integer('task_id'); // Yellow FK
            $table->integer('task_priority')->nullable();
            $table->integer('assigned_by'); // Yellow FK
            $table->integer('user_id'); // Yellow FK
            $table->timestamp('assigned_at')->useCurrent();
            
            // Common Fields
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('status')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_task_assignments');
    }
};