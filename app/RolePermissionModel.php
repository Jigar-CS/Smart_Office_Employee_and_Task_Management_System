<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['role_id', 'permission_key', 'permission_name', 'status'];

    public function getallrolepermissions($data = [])
    {
        $query = DB::table('role_permissions as rp')->leftJoin('roles as r', 'r.id', '=', 'rp.role_id')->select('rp.*', 'r.name as role_name');

        if (array_key_exists('role_id', $data) && isset($data['role_id'])) {
            $query = $query->where('rp.role_id', $data['role_id']);
        }

        if (!(array_key_exists('is_admin', $data) && isset($data['is_admin']) && $data['is_admin'] == 1)) {
            $query = $query->where('rp.status', 1);
        }

        if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
            $total_count = $query->count();
            $result = $query->offset($data['offset'])->limit($data['limit'])->get();
            return ['data' => $result, 'total_count' => $total_count];
        }

        return $query->get();
    }
}
