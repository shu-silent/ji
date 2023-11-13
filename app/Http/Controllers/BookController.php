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


    public function show($id, Request $request)
    {
        // $id を使用してデータベースから該当の BOOK の詳細情報を取得
        $book = Book::find($id);
    
        // キャッシュキーを生成
        $cacheKey = 'ogp_info_' . $id;

    
        // キャッシュが有効かどうかをチェック
        if (Cache::has($cacheKey)) {
            // キャッシュが存在する場合、それを使用
            $cachedData = Cache::get($cacheKey);
            $relatedItems = $cachedData['relatedItems'];
        } else {
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
        }
    
        // その他の必要な処理
        // データをセッションに保存
        session(['relatedItems' => $relatedItems]);
    
        // ビューにデータを渡して詳細情報を表示
        return view('books.detail', ['book' => $book, 'relatedItems' => $relatedItems]);
    }
                                
    private function getOGPInfo($url)
    {
        // $url が存在しない場合は空の配列を返す
        if (empty($url)) {
            return [
                'title' => 'Error',
                'description' => 'Non-existent URL',
                'image' => '',
            ];
        }

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
    // book削除、該当book_idをもつアイテム削除
    public function deleteBook($bookId) {
        // Bookモデルから該当の本を削除
        Book::where('id', $bookId)->delete();

        // アイテムモデルから該当のbook_idを持つアイテムを削除
        Item::where('book_id', $bookId)->delete();

        // 削除が成功したらリダイレクトなどの処理を追加

        return view('home');
    }

    // book名変更
    public function editBook(Request $request, $bookId)
    {
        // リクエストから送信されたデータを取得
        $data = $request->all();

        // バリデーション
        $request->validate([
            'name' => 'required|max:100',
        ]);

        // ブックモデルを使用して指定されたブックを取得
        $book = Book::find($bookId);

        if (!$book) {
            return redirect()->back()->with('error', '指定されたブックが見つかりません。');
        }

        // Book名を更新
        $book->name = $data['name'];
        $book->save();

        return redirect()->route('book.detail', ['id' => $book->id]);
    }
}