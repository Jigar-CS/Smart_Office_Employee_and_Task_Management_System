<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_task_assignments', function (Blueprint $table) {
            $table->integer('task_priority')->nullable()->after('task_id');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_task_assignments', function (Blueprint $table) {
            $table->dropColumn('task_priority');
        });
    }
};
