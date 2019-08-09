<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Transaction extends MomonationBaseModel {
    protected $fillable = [
        'sender',
        'receiver',
        'amount',
        'by_user',
        'cooked',
    ];

    public function sender() {
        return $this->belongsTo('App\User', 'sender');
    }

    public function receiver() {
        return $this->belongsTo('App\User', 'receiver');
    }

    public function feed() {
        return $this->hasOne('App\Models\Feed');
    }
}