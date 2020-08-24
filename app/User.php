<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
    * このユーザが所有する投稿。(Micropostsモデルとの関係を定義)
    */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
    * このユーザに関係するモデルの件数をロードする。
    */
    public function loadRelationshipCounts()
    {
        $this->loadCount('microposts');
    }
}
