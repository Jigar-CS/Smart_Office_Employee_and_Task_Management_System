<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    protected $table = 'tbl_departments';
    protected $primaryKey = 'department_id';
    protected $guarded = [];
}