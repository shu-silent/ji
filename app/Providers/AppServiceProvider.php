<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Contracts\Events\Dispatcher;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     //
    // }



    // public function boot(Dispatcher $events)
    // {
    //     $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
    //         // メニューにカスタムデータを追加
    //         $user = Auth::user();
    //         if ($user) {
    //             $books = DB::table('books')->where('user_id', $user->id)->get();

    //             // Booksメニューアイテムを作成し、データを追加
    //             $event->menu->add([
    //                 'text' => 'Books',
    //                 'url' => 'books',
    //                 'icon' => 'fas fa-book',
    //                 'submenu' => collect($books)->map(function ($book) {
    //                     return [
    //                         'text' => $book->name,
    //                         'url' => '#', // 本の詳細ページへのURLを設定する必要があります
    //                     ];
    //                 })->all(),
    //             ]);
    //         }
    //     });
    // }

        public function boot(Dispatcher $events)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            // メニューにカスタムデータを追加
            $user = Auth::user();
            if ($user) {
                $books = DB::table('books')->where('user_id', $user->id)->get();

                // 個別の本を独立したメニューアイテムとして追加
                foreach ($books as $book) {
                    $event->menu->add([
                        'text' => $book->name,
                        'url' => route('book.detail', ['id' => $book->id]), // 本の詳細ページへのURLを設定する必要があります
                        // 他のオプションも設定できます
                    ]);
                }
            }
        });
    }

}
