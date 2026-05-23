<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'task_code', 'created_by', 'assigned_to', 'department_id', 'task_status_id',
        'priority_id', 'title', 'description', 'due_date', 'status'
    ];

    public function getalltasks($data = [])
    {
        $query = DB::table('tasks as t')
            ->leftJoin('users as u', 'u.id', '=', 't.assigned_to')
            ->leftJoin('task_statuses as ts', 'ts.id', '=', 't.task_status_id')
            ->leftJoin('priorities as p', 'p.id', '=', 't.priority_id')
            ->leftJoin('departments as d', 'd.id', '=', 't.department_id')
            ->select('t.*', 'u.name as assigned_to_name', 'ts.label as task_status_name', 'p.label as priority_name', 'd.name as department_name');

        if (array_key_exists('search', $data) && isset($data['search'])) {
            $query = $query->where(function ($q) use ($data) {
                $q->where('t.task_code', 'like', '%' . $data['search'] . '%')
                  ->orWhere('t.title', 'like', '%' . $data['search'] . '%');
            });
        }

        if (array_key_exists('task_status_id', $data) && isset($data['task_status_id'])) {
            $query = $query->where('t.task_status_id', $data['task_status_id']);
        }

        if (array_key_exists('assigned_to', $data) && isset($data['assigned_to'])) {
            $query = $query->where('t.assigned_to', $data['assigned_to']);
        }

        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereDate('t.created_at', '>=', $data['from_date'])->whereDate('t.created_at', '<=', $data['to_date']);
        }

        if (array_key_exists('sort_column', $data) && isset($data['sort_column']) && array_key_exists('sort_dir', $data) && isset($data['sort_dir'])) {
            $query = $query->orderBy($data['sort_column'], $data['sort_dir']);
        } else {
            $query = $query->orderBy('t.id', 'desc');
        }

        if (!(array_key_exists('is_admin', $data) && isset($data['is_admin']) && $data['is_admin'] == 1)) {
            $query = $query->where('t.status', 1);
        }

        if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
            $total_count = $query->count();
            $result = $query->offset($data['offset'])->limit($data['limit'])->get();
            return ['data' => $result, 'total_count' => $total_count];
        }

        return $query->get();
    }
}
