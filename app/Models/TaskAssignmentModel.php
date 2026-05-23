<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignmentModel extends Model
{
    protected $table = 'tbl_task_assignments';
    protected $primaryKey = 'assignment_id';
    protected $guarded = [];
}