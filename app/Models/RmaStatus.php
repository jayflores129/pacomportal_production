<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaStatus extends Model
{  
    protected $table = "rma_status";
    use HasFactory;

    protected $fillable = [
            'status', 'updated_by', 'courier', 'consignment_note', 'note', 'user_id', 'rma_id', 
    ];
}
