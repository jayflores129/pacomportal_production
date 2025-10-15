<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'file_id',
        'name',
        'created_at',
        'updated_at'
    ];

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }
}
