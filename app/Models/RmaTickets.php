<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RmaTickets extends Model
{
    protected $table = "rma_tickets";
    use HasFactory;

    protected $fillable = [
            'requested_date', 'has_quotation', 'has_confirmed', 'requester_name', 'requester_phone', 'requester_company', 'requester_email', 'po_number', 'requester_fax', 'company_name', 'company_phone', 'company_fax', 'company_address', 'company_country', 'currency', 'company_isvar', 'notify'
    ];
  
    public function user(): BelongsTo
    {
    	return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RmaItems::class, 'rma_id', 'id');
    }

    public function rma_status(): HasMany
    {
        return $this->hasMany(RmaStatus::class, 'rma_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RmaComments::class, 'rma_id', 'id');
    }

    public function getBtnColor() {
        switch ($this->status) {
            case 'Under Reviewed':
              $class = 'btn-default btn-open';
              break;
           case 'Open':
              $class = 'btn-default btn-open';
              break;
         case 'Confirmed':
              $class = 'btn-default btn-cs';
              break;
           case 'Under Review':
              $class = 'btn-default btn-open';
              break;
           case 'Received':
              $class = 'btn-default btn-r';
              break;
           case 'To Be Confirmed':
              $class = 'btn-default btn-ps';
              break;
              
           case 'Submitted':
              $class = 'btn-default btn-cs';
              break; 

             case 'Completed':
              $class = 'btn-default btn-r';
              break;
             case 'Partially Shipped':
              $class = 'btn-default btn-rp';
              break; 
             case 'Shipped':
              $class = 'btn-default btn-rp';
              break;  

             case 'Cancelled':
              $class = 'btn-default btn-rt';
              break;  
            
            default:
              $class = '';
              break;
            }

            return $class;
    }

}
