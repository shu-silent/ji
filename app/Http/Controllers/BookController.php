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
        return view('books.add');
    }

    public function add(Request $request)
    {
        // バリデーション
        $this->validate($request, [
            'name' => 'required|max:100',
        ]);

        // 商品登録
        $book = Book::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name
        ]);

        return redirect()->route('book.detail', ['id' => $book->id]);
        
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

    public function show($id)
    {
        // $id を使用してデータベースから該当の BOOK の詳細情報を取得
        $book = Book::find($id);

        // ビューにデータを渡して詳細情報を表示
        return view('books.detail', ['book' => $book]);
    }



}