<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Village extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    //

    /**
     * [user Relationship to User]
     * @return [type] [description]
     */
    public function address()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District');
    }

}
