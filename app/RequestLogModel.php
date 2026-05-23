<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestLogModel extends Model
{
    protected $table = 'request_logs';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['method', 'url', 'ip_address', 'request_payload', 'response_code', 'user_id'];
}
