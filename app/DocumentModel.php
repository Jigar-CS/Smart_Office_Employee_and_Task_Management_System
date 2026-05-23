<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['owner_type', 'owner_id', 'title', 'file_name', 'file_path', 'file_type', 'file_size', 'uploaded_by', 'status'];

    public function getalldocuments($data = [])
    {
        $query = DB::table('documents as d')->leftJoin('users as u', 'u.id', '=', 'd.uploaded_by')->select('d.*', 'u.name as uploaded_by_name');

        if (array_key_exists('search', $data) && isset($data['search'])) {
            $query = $query->where('d.title', 'like', '%' . $data['search'] . '%');
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
