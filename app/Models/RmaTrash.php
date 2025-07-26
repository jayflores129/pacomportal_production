<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaTrash extends Model
{
    protected $table = "rma_trash";
    use HasFactory;

    protected $fillable = [
        'data', 'deleted_by'
    ];
}
