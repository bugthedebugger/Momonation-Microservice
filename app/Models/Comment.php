<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Comment extends MomonationBaseModel {
    protected $fillable = [
        'feed_id',
        'comment',
        'user_id',
    ];

    public function feed() {
        return $this->belongsTo('App\Models\Feed');    
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}