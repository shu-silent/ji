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


    public function show($id)
    {
        // $id を使用してデータベースから該当の BOOK の詳細情報を取得
        $book = Book::find($id);

        // 同じ book_id を持つアイテムを取得
        $user = Auth::user();
        $relatedItems = Item::where('user_id', $user->id)
                            ->where('book_id', $id)
                            ->get();

        // スクレイピングを実行してOGP情報を取得
        foreach ($relatedItems as $item) {
            $url = $item->url;
            $ogp = $this->getOGPInfo($url);
            // $ogp には 'title', 'description', 'image' などが含まれると仮定
            $item->ogp = $ogp;
        }
        
        // その他の必要な処理
        // データをセッションに保存
        session(['relatedItems' => $relatedItems]);

        // ビューにデータを渡して詳細情報を表示
        return view('books.detail', ['book' => $book, 'relatedItems' => $relatedItems]);
    }

    private function getOGPInfo($url)
    {

        // // HttpClient を使用して Web ページをスクレイピング
        // $client = HttpClient::create();
        // $response = $client->request('GET', $url);

        // // Web ページのコンテンツを取得
        // $content = $response->getContent();

        // // OGP タグから情報を抽出
        // $ogp = [
        //     'title' => 'Title of the page',
        //     'description' => 'Description of the page',
        //     'image' => 'URL of the image',
        // ];

        // return $ogp;

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
}