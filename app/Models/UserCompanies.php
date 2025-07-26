<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class UserCompanies extends Model
{
    use HasFactory;
    protected $table = "user_companies";

    protected $fillable = [
        'primary', 'user_id', 'company_id'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'company_id', 'id');
    }

    public function company() : BelongsTo
    {
        //$company = Company::where('id', $company_id)->get();

        return $this->belongsTo(Company::class);
    }

    public static function hasCompany($id)
    {
        $data = UserCompanies::where('user_id', $id)->first();

        if($data) {
            return true;
        }
        return false;
    }


}
