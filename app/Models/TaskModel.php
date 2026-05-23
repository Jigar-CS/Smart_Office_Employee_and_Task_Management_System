<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    protected $table = 'tbl_tasks';
    protected $primaryKey = 'task_id';
    protected $guarded = [];
}