<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityModel extends Model
{
    protected $table = 'tbl_priorities';
    protected $primaryKey = 'priority_id';
    protected $guarded = [];
}