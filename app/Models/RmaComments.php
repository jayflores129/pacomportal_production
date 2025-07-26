<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaComments extends Model
{
    protected $table = "rma_comments";
    use HasFactory;

    protected $fillable = [
        'comment', 'rma_id', 'user_id'
    ];

    
}
