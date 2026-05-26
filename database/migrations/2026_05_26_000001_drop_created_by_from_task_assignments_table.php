<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tbl_task_assignments', 'created_by')) {
            Schema::table('tbl_task_assignments', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('tbl_task_assignments', 'created_by')) {
            Schema::table('tbl_task_assignments', function (Blueprint $table) {
                $table->integer('created_by')->nullable()->after('created_at');
            });
        }
    }
};