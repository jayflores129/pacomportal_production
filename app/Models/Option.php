<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    //


	public function new_user_email()
	{
		return $user_notification   = Option::where('key','notification_new_user_email' )->value('value');  
         
	}  

	public function new_repair_email()
	{
		return $repair_notification = Option::where('key','notification_new_repair_email' )->value('value');
	} 
}
