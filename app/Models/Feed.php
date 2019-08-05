<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Feed extends MomonationBaseModel {
    protected $fillable = [
        'sender',
        'receiver',
        'title',
        'description',
        'transaction_id',
    ];

    public function senderUser() {
        return $this->belongsTo('App\User', 'sender');
    }

    public function receiverUser() {
        return $this->belongsTo('App\User', 'receiver');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }

    public function likes() {
        return $this->hasMany('App\Models\Like');
    }

    public function likeCount() {
        try {
            return $this->likes->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function transaction() {
        return $this->belongsTo('App\Models\Transaction');
    }
}