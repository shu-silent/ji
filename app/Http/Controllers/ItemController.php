<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧取得
        // $items = Item::all();

        // ユーザーを特定し、そのユーザーの所有データのみを表示
        $user = auth::user();

        if($user) {
            $items = DB::table('items')->where('user_id',$user->id)->get();
        }

        return view('item.index', compact('items'));
    }

    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request, [
                'name' => 'required|max:100',
            ]);

            // 商品登録
            Item::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'type' => $request->type,
                'detail' => $request->detail,
            ]);

            return redirect('/items');
        }

        return view('item.add');
    }

    public function view(Request $request)
    {
        
        $user = Auth::user();

        Item::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'book_id' => $request->book_id,

        ]);

        
        // リダイレクト先を指定（詳細ページに戻るなど）

        return redirect()->route('book.detail', ['id' => $request->book_id]);

    }

    public function deleteItem(Request $request)
    {
        $itemId = $request->input('itemId');
        // アイテムを削除する処理（Eloquentを使用する例）
        Item::where('id', $itemId)->delete();
        
        return response()->json(['message' => 'Item deleted successfully']);
    }

    
}
