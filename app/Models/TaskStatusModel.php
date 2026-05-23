<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatusModel extends Model
{
    protected $table = 'tbl_task_status';
    protected $primaryKey = 'task_status_id';
    protected $guarded = [];
}