<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\MailResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'country', 'phone', 'company', 'blocked','email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getId()
    {
      return $this->id;
    }

    public function repairs()
    {
        return $this->hasMany('App\Repairs');
    }

     public function downloads()
    {
        return $this->hasMany('App\Download');
    }
    
    public function isCustomer() 
    {
         foreach($this->roles()->get() as $role )
         {
            if( $role->name === 'SPG Internal User')
            {
                return true;
            }
            
         }

     return false;
    }

    /**
     * Check user' role
     * @return string type of role
     */
    public function getUserRole() 
    {
       
        if( $this->hasRole('admin'))
        {           
            return $user_role = 'admin';
        }
        elseif( $this->hasRole('staff')) 
        {
            return $user_role =  'staff';
        }
        elseif( $this->hasRole('customer') ) 
        {
            return $user_role =  'customer';
        } else {
                return $user_role =  'N/A';
        }
    }

    public function isAdmin()
    {
        if( $this->hasRole('admin') ) {
            return true;
        }
         
        return false;
    }

    public function isEditor($email)
    {
        $rma_editor_email = Option::where('key','rma_editor_permission_email' )->value('value');

        if($email === $rma_editor_email) {
            return true;
        }
        return false;
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
    
    public function softwareTickets()
    {
        return $this->hasMany(Software::class, 'user_id', 'id' );
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function myCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     *  Check if user is an approve user
     */
    public function isApproved($id)
    {
        $hasApprove = User::find($id)->status;

        if($hasApprove == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     *  Check if user is from Spain
     */
    public static function isSpain($userID)
    {
        $user = User::where('id', $userID)->value('country');

        if($user == "Spain") {
            return true;
        }
        else {
            return false;
        }
    }

        /**
     *  Check if user is from Spain
     */
    public function spainTeam($userID)
    {
        $user = User::where('id', $userID)->value('country');

        if($user == "Spain") {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }
}
