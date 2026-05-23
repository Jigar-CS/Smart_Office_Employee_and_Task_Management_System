<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'tbl_roles';
    protected $primaryKey = 'role_id';
    protected $guarded = [];
}