<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('permission_key');
            $table->string('permission_name');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['role_id', 'permission_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
