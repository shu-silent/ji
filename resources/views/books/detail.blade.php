@extends('adminlte::page')

@section('title', 'contents')

@section('content_header')
    <div style="display: flex; align-items: center;">
        <h1>{{ $book->name }}</h1>
        <button id="add-item-button" class="btn btn-default">+</button>  
    </div>
@stop

@section('content')
    
    
    @if(session('relatedItems'))
    <ul>
        @foreach(session('relatedItems') as $item)
            <li>
                <a href="{{ $item->url }}" style="text-decoration: none; color: inherit; display: block; width: 100%; height: 100%;" class="btn btn-default">
                    <div style="width: 500px; height: 130px; ">
                        <div style="width: 33.33%; height: 100%; float: left;">
                            <img src="{{ $item->ogp['image'] }}" alt="No Image" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                        </div>
                        <div style="width: 66.67%; height: 100%; float: left;">
                            <div style="width: 100%; height: 30%; font-weight: bold;">
                                {{ $item->ogp['title'] }}
                            </div>
                            <div style="height: 70%; width: 100%; overflow: hidden; position: relative;">
                                <div style="height: 100%; position: absolute; top: 0; left: 0; background: linear-gradient(to right, rgba(255, 255, 255, 0), white 70%);"></div>
                                {{ $item->ogp['description'] }}
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
    @else
        <p>No related items found.</p>
    @endif

    <!-- ポップアップのHTML -->
    <div id="popup" class="card card-primary" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; width: 600px; z-index: 2;">
        <button id="close-popup-button" class="btn btn-default">×</button>
        <h2>Add Item</h2>
        <form id="item-form" method="POST" action="">
        @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">URL</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="URL">
                </div>

            </div>
            
            <input type="hidden" name="book_id" id="book-id-field" value="{{ $book->id }}">

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Add Item</button>
            </div>

            <!-- 他のフォームフィールド -->
        </form>
    </div>

@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

<style>
    #add-item-button {
        /* position: fixed; ボタンの位置を固定 */
        /* top: 120px; 上からの距離を調整 */
        /* right: 10px; 右からの距離を調整 */
        /* z-index: 1; ボタンの表示順序を設定 */
        margin-left: 10px;
    }
</style>

@stop

@section('js')
    <!-- プラスボタンをクリックしたときにポップアップを表示 -->
    <script>
        $(document).ready(function () {
            $('#add-item-button').click(function () {
                // ポップアップを表示する
                $('#popup').show();
            });
        });
    </script>

    <!-- ポップアップを閉じるボタンのクリックイベント -->
    <script>
        $(document).ready(function () {
            $('#close-popup-button').click(function () {
                // ポップアップを非表示にする
                $('#popup').hide();
            });
        });
    </script>

@stop