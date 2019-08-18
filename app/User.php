<?php

namespace App;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;

    protected $connection = 'mysql';
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'admin', 'created_at', 'updated_at', 'priviege_id', 'verified',
    ];

    public function social()
    {
        return $this->hasMany('App\Models\SocialAuth');
    }

    public function asSenderFeed() {
        return $this->hasMany('App\Models\Feed', 'sender');
    }

    public function asReceiverFeed() {
        return $this->hasMany('App\Models\Feed', 'receiver');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }

    public function likes() {
        return $this->hasMany('App\Models\Like');
    }

    public function bank() {
        return $this->hasOne('App\Models\Momobank');
    }

    public function receivedTransactions() {
        return $this->hasMany('App\Models\Transaction', 'receiver');
    }

    public function info() {
        if($this->social()->count() != 0)
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'avatar' => $this->social()->first()->avatar,
                'momo' => (int)$this->receivedTransactions()
                                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                                ->where('created_at', '<=', Carbon::now()->endOfMonth())
                                ->where('cooked', true)
                                ->sum('amount'),
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'avatar' => null,
                'momo' => 0,
            ];
        }
    }
}
