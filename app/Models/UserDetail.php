<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    //protected $table = 'user_details';
    // protected $touches = ['users'];
    // 
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'address2', 'state', 'city', 'zipcode', 'fax', 'sms_number', 'office_phone', 'website'
    ];

    // public function address() {

    // 	A
    // 	return $this->address;
    // }
}
