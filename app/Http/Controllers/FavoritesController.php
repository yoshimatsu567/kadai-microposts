<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
    *投稿をお気に入りに登録するアクション
    */
    public function store($id)
    {
        // 認証済みユーザがidの投稿をお気に入りに登録する
        \Auth::user()->favorite($id);
        // 前のURLへリダイレクト
        return back();
    }
    
    /**
    *お気に入りを解除するアクション
    */
    public function destroy($id)
    {
        // 認証済みユーザがidの投稿のお気に入りを解除する
        \Auth::user()->unfavorite($id);
        // 前のURLへリダイレクト
        return back();
    }
}
