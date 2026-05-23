<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['module_name', 'action_name', 'record_id', 'old_value', 'new_value', 'action_by'];
}
