<?php
namespace App\Models;

use App\Models\MomonationBaseModel;

class Leaderboard extends MomonationBaseModel {

    protected $connection = 'momonation';

    protected $fillable = [
        'date',
    ];

    public function users() {
        $database = $this->getConnection()->getDatabaseName();
        return $this->belongsToMany('App\User', $database.'.leaderboard_user');
    }

}