<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SocialAuth extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'provider',
        'oauth_id',
        'avatar',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
