<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $table = 'files';

    public function categoryName() 
    {
    	 return $this->hasOne('App\Models\Category', 'id', 'category' );
    }

    public function releaseName() 
    {
    	 return $this->hasOne('App\Models\Release', 'id', 'release_id' );
    }

    public function download()
    {
    	return $this->hasMany('App\Models\Download');
    }
}
