<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketLog extends Model
{
    protected $table = 'ticket_logs';


    public function product() {

    	return $this->belongsTo(Products::class, 'product_id', 'id' );
    }
}
