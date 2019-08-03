<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Setting extends MomonationBaseModel {
    
    protected $fillable = [
        'daily_transaction_limit',
        'momo_transfer_limit',
        'auto_refill_limit',
        'initialization_limit',
    ];

}