<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Momobank extends MomonationBaseModel {
    protected $fillable = [
        'user_id',
        'raw',
        'cooked',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}