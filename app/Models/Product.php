<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function softwareTickets()
    {
        return $this->hasMany(Software::class, 'product_id', 'id' );
    }
}
