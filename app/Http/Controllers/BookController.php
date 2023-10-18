<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use Illuminate\Support\Facades\DB;



class BookController extends Controller
{
    public function view()
    {
        return view('book.book');
    }

    public function add(Request $request)
    {
        // バリデーション
        $this->validate($request, [
            'name' => 'required|max:100',
        ]);

        // 商品登録
        Book::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name
        ]);

        return redirect('');
        
    }

    // public function index()
    // {
    //     // 現在ログイン中のユーザーに関連するbooksデータを取得
    //     $user = auth::user();

    //     if($user) {
    //         $books = DB::table('books')->where('user_id',$user->id)->get();
    //     }
        
    //     config(['adminlte.custom_data' => $books]);

    //     return view('/home');
    //     // return view('/layouts/adminlte', compact('books'));
    // }



}