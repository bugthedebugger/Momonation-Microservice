<?php
namespace App\Models;

use App\Models\MomonationBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends MomonationBaseModel {
    
    use SoftDeletes;

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