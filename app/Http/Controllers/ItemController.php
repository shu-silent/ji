<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Support\Facades\Cache;


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

    public function view($id, Request $request)
    {
        
        $user = Auth::user();

        Item::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'book_id' => $request->book_id,

        ]);

        // $id を使用してデータベースから該当の BOOK の詳細情報を取得
        $book = Book::find($id);

        // キャッシュキーを生成
        $cacheKey = 'ogp_info_' . $id;

      // キャッシュをクリア
      Cache::forget($cacheKey);

        // キャッシュが有効かどうかをチェック
        // if (Cache::has($cacheKey)) {
        //     // キャッシュが存在する場合、それを使用
        //     $cachedData = Cache::get($cacheKey);
        //     $relatedItems = $cachedData['relatedItems'];
        // } else {
            // キャッシュが存在しない場合、スクレイピングを実行してOGP情報を取得
            $user = Auth::user();
            $relatedItems = Item::where('user_id', $user->id)
                                ->where('book_id', $id)
                                ->get();
            foreach ($relatedItems as $item) {
                $url = $item->url;
                $ogp = $this->getOGPInfo($url);
                // $ogp には 'title', 'description', 'image' などが含まれると仮定
                $item->ogp = $ogp;
            }
    
            // \Log::info('Related Items:', $relatedItems);
            // キャッシュに保存（有効期間を設定することもできます）
            Cache::put($cacheKey, ['relatedItems' => $relatedItems], now()->addMinutes(1440)); // 60分間キャッシュを保存
        // }
    
        // その他の必要な処理
        // データをセッションに保存
        session(['relatedItems' => $relatedItems]);
    
        // ビューにデータを渡して詳細情報を表示
        return view('books.detail', ['book' => $book, 'relatedItems' => $relatedItems]);
    }

    private function getOGPInfo($url)
    {


        try {
            // HttpClientを使用してURLにアクセス
            $client = HttpClient::create();
            $response = $client->request('GET', $url);
    
            // レスポンスのコンテンツを取得
            $content = $response->getContent();
    
            // DomCrawlerを使用してOGP情報を抽出
            $crawler = new Crawler($content);
            $ogp = [
                'title' => $crawler->filterXPath('//meta[@property="og:title"]')->attr('content'),
                'description' => $crawler->filterXPath('//meta[@property="og:description"]')->attr('content'),
                'image' => $crawler->filterXPath('//meta[@property="og:image"]')->attr('content'),
            ];
    
            return $ogp;
        } catch (\Exception $e) {
            // エラーが発生した場合の処理を追加
            return [
                'title' => 'Error',
                'description' => 'Failed to retrieve OGP information',
                'image' => '',
            ];
   
            }

    }

    
        public function deleteItem(Request $request)
    {
        $itemId = $request->input('itemId');
        
        // アイテムを取得
        $item = Item::find($itemId);
        
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // $idに格納
        $id = $item->book_id;

        // キャッシュキーを生成
        $cacheKey = 'ogp_info_' . $id;

        // キャッシュをクリア
        Cache::forget($cacheKey);
        
        // アイテムを削除
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }

    
}
