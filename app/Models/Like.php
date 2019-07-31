<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Like extends MomonationBaseModel {
    protected $fillable = [
        'feed_id',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }
}