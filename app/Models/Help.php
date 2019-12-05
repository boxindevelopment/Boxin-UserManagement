<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Help extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
