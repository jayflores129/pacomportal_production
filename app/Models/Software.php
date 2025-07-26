<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Software extends Model
{
    protected $table = 'software_tickets';

    protected $dates = [
        'created_at',
        'updated_at',
        // your other new column
    ];

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function assign()
    {
    	return $this->belongsTo(User::class, 'assigned_to');
    }

    public function product()
    {
    	return $this->hasOne('App\Models\Product','id', 'product_id');
    }

    /**
     * Get the user's first name.
     *
     * @param  integer  the id of the software ticket
     * @return string
     */
    public static function fullname($id)
    {
        return User::find($id)->firstname . ' ' . User::find($id)->lastname;
        
    }

    public function files() 
    {
         return $this->hasMany(SoftwareAttachment::class);
    }


}
