<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatusLogModel extends Model
{
    protected $table = 'tbl_task_status_log';
    protected $primaryKey = 'status_log_id';
    protected $guarded = [];
}