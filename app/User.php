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
    * このユーザがフォロー中のユーザ。 (Userモデルとの関係を定義)
    */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    /**
    * このユーザをフォロー中のユーザ。 (Userモデルとの関係を定義)
    */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /**
    * $userIdで指定されたユーザをフォローする。
    * 
    * @param int $userId
    * @return bool
    */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        //  相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) 
        {
            //すでにフォローしていれば何もしない
            return false;
        }
        else 
        {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /**
    * $userIdで指定されたユーザをアンフォローする。
    * 
    * @param int $userId
    * @return bool
    */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) 
        {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        }
        else 
        {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    /**
    * 指定された $userIdのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
    * 
    * @param int $userId
    * @return bool
    */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id',$userId)->exists();
    }
    
    /**
    * このユーザとフォロー中ユーザの投稿に絞り込む。
    */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザの idもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    /**
    * このユーザに関係するモデルの件数をロードする。
    */
    public function loadRelationshipCounts()
    {
        // 'favorites'追加
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
        
    }
    
    
    /**
    * このユーザがお気に入りしている投稿達
    */
    public function favorites() 
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    
    
    /**
    * $micropostsIdで指定された投稿をお気に入りに登録する。
    * 
    */
    public function favorite($micropostId) 
    {
        //すでにお気に入りしているかの確認
        $exist = $this->is_favorite($micropostId);
        
        
        if($exist) 
        {
            //すでにお気に入りしてたら何もしない
            return false;
        }
        else 
        {
            //お気に入りしてなければお気に入り登録する
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    /**
    * $micropostsIdで指定された投稿のお気に入り登録を解除する。
    */
    public function unfavorite($micropostId) 
    {
        //すでにお気に入りしているかの確認
        $exist = $this->is_favorite($micropostId);
        
        
        if ($exist) 
        {
            // すでにお気に入りしていればお気に入り登録解除
            $this->favorites()->detach($micropostId);
            return true;
        }
        else 
        {
            // お気に入りしていなければ何もしない
            return false;
        }
    }
    /**
    *指定された$micropostIdの投稿をこのユーザがお気に入り中か調べる。お気に入り中ならtrueを返す。
    */
    public function is_favorite($micropostId)
    {
        // お気に入りにしている投稿の中に $userIdのものが存在するか
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
}
