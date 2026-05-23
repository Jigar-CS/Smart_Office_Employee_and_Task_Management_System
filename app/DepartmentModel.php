<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['name', 'description', 'status'];

    public function getalldepartment($data = [])
    {
        $query = DB::table('departments as d')->select('d.*');

        if (array_key_exists('search', $data) && isset($data['search'])) {
            $query = $query->where('d.name', 'like', '%' . $data['search'] . '%');
        }

        if (array_key_exists('sort_column', $data) && isset($data['sort_column']) && array_key_exists('sort_dir', $data) && isset($data['sort_dir'])) {
            $query = $query->orderBy($data['sort_column'], $data['sort_dir']);
        } else {
            $query = $query->orderBy('d.id', 'asc');
        }

        if (!(array_key_exists('is_admin', $data) && isset($data['is_admin']) && $data['is_admin'] == 1)) {
            $query = $query->where('d.status', 1);
        }

        if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
            $total_count = $query->count();
            $result = $query->offset($data['offset'])->limit($data['limit'])->get();
            return ['data' => $result, 'total_count' => $total_count];
        }

        return $query->get();
    }
}
