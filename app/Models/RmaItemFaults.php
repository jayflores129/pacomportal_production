<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaItemFaults extends Model
{
    protected $table = "rma_item_faults";
    use HasFactory;
    protected $fillable = [
            'fault'
    ];
    


}
