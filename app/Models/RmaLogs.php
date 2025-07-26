<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaLogs extends Model
{
    protected $table = "rma_logs";
    use HasFactory;

    protected $fillable = [
        'type', 'description', 'old_value', 'new_value', 'action', 'user_id', 'rma_item_id', 'rma_id', 
    ];
}
