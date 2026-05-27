<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const IST_OFFSET = '05:30:00';

    private const TABLE_COLUMNS = [
        'tbl_users' => [
            'email_verified_at' => 'DATETIME NULL DEFAULT NULL',
            'last_login_at' => 'DATETIME NULL DEFAULT NULL',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_priorities' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_roles' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_task_status' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_departments' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_tasks' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_task_assignments' => [
            'assigned_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_task_status_log' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'tbl_documents' => [
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
        'personal_access_tokens' => [
            'last_used_at' => 'DATETIME NULL DEFAULT NULL',
            'expires_at' => 'DATETIME NULL DEFAULT NULL',
            'created_at' => 'DATETIME NULL DEFAULT NULL',
            'updated_at' => 'DATETIME NULL DEFAULT NULL',
        ],
    ];

    public function up(): void
    {
        foreach (self::TABLE_COLUMNS as $table => $columns) {
            foreach (array_keys($columns) as $column) {
                DB::statement(sprintf(
                    "UPDATE `%s` SET `%s` = ADDTIME(`%s`, '%s') WHERE `%s` IS NOT NULL",
                    $table,
                    $column,
                    $column,
                    self::IST_OFFSET,
                    $column
                ));
            }

            foreach ($columns as $column => $definition) {
                DB::statement(sprintf(
                    'ALTER TABLE `%s` MODIFY `%s` %s',
                    $table,
                    $column,
                    $definition
                ));
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLE_COLUMNS as $table => $columns) {
            foreach (array_keys($columns) as $column) {
                DB::statement(sprintf(
                    "UPDATE `%s` SET `%s` = SUBTIME(`%s`, '%s') WHERE `%s` IS NOT NULL",
                    $table,
                    $column,
                    $column,
                    self::IST_OFFSET,
                    $column
                ));
            }

            foreach ($columns as $column => $definition) {
                $legacyDefinition = match ($column) {
                    'created_at', 'assigned_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
                    default => 'TIMESTAMP NULL DEFAULT NULL',
                };

                DB::statement(sprintf(
                    'ALTER TABLE `%s` MODIFY `%s` %s',
                    $table,
                    $column,
                    $legacyDefinition
                ));
            }
        }
    }
};