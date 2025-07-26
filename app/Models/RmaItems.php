<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RmaItems extends Model
{
    protected $table = "rma_items";
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model', 'date_purchased', 'under_warranty', 'original_order_date', 'fault_described_by_customer', 'serial_number', 'root_cause_analysis', 'pacom_fault_description', 'pacom_comment', 'received_date', 'repaired_date', 'status', 'invalid_serial_number', 'repair_cost'
    ];


    public function faults(): HasMany
    {
        return $this->hasMany(RmaItemFaults::class, 'rma_items_id', 'id');
    }
    
}
