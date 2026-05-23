<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentModel extends Model
{
    protected $table = 'tbl_documents';
    protected $primaryKey = 'document_id';
    protected $guarded = [];
}