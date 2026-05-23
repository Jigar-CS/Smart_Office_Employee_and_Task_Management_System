<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class TaskAssignmentModel extends Model
{
    
    protected $table = 'task_assignments';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['task_id', 'assigned_to', 'assigned_by', 'assigned_at', 'remarks'];

    public function getalltaskassignments($data = [])
    {
        $query = DB::table('task_assignments as ta')
            ->leftJoin('tasks as t', 't.id', '=', 'ta.task_id')
            ->leftJoin('users as u1', 'u1.id', '=', 'ta.assigned_to')
            ->leftJoin('users as u2', 'u2.id', '=', 'ta.assigned_by')
            ->select('ta.*', 't.title as task_title', 'u1.name as assigned_to_name', 'u2.name as assigned_by_name');

        if (array_key_exists('task_id', $data) && isset($data['task_id'])) {
            $query = $query->where('ta.task_id', $data['task_id']);
        }

        if (!(array_key_exists('is_admin', $data) && isset($data['is_admin']) && $data['is_admin'] == 1)) {
            $query = $query->whereNull('ta.deleted_at');
        }

        if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
            $total_count = $query->count();
            $result = $query->offset($data['offset'])->limit($data['limit'])->get();
            return ['data' => $result, 'total_count' => $total_count];
        }

        return $query->get();
    }
}
