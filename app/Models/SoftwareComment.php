<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareComment extends Model
{
    protected $table = 'software_comments';

    public function attachment()
    {
        return $this->hasOne(SoftwareAttachment::class, 'attachmen_id');
    }
}
