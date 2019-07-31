<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Transaction extends MomonationBaseModel {
    protected $fillable = [
        'sender',
        'receiver',
        'amount',
    ];

    public function sender() {
        return $this->belongsTo('App\User', 'sender');
    }

    public function receiver() {
        return $this->belongsTo('App\User', 'receiver');
    }
}