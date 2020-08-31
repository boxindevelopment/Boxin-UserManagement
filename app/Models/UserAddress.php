<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    //

    /**
     * [user Relationship to User]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function village()
    {
        return $this->belongsTo('App\Models\Village');
    }

}
