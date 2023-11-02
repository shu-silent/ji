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
    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; align-items: center; gap: 20px;">
        @foreach(session('relatedItems') as $item)
            <li style="width: 500px; margin-bottom: 20px; position: relative;"> <!-- セットのdivの大きさを可変、上下の余白追加 -->
                <a href="{{ $item->url }}" class="btn btn-default" style="text-decoration: none; color: inherit; display: block;">
                    <!-- × ボタンを追加 -->
                    <a class="delete-item" data-item-id="{{ $item->id }}" style="position: absolute; top: 10px; right: 10px;">×</a>
                    <div style="width: 100%; height: 100%; padding: 10px; display: flex; gap: 10px;">
                        <div style="width: 30%; height: 100%;">
                            <img src="{{ $item->ogp['image'] }}" alt="No Image" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="width: 70%; display: flex; flex-direction: column;">
                            <div style="height: 40%; text-align: center; padding: 5px; font-weight: bold;">
                                {{ $item->ogp['title'] }}
                            </div>
                            <div style="height: 60%; text-align: center; padding: 5px; position: relative;">
                                <div style="height: 100%; position: absolute; top: 0; left: 0; background: linear-gradient(to top, transparent 0%, #f1f1f1 100%);"></div>
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

    <script>
        $(document).ready(function() {
            $('.delete-item').on('click', function() {
                var itemId = $(this).data('item-id');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('delete.item') }}', // ルート名を使用
                    data: {itemId: itemId, _token: '{{ csrf_token() }}'},
                    success: function(data) {
                        // アイテムを非表示にする処理
                        $(`.delete-item[data-item-id="${itemId}"]`).closest('li').remove();
                    },
                    error: function() {
                        // エラーハンドリング
                    }
                });
            });
        });

    </script>

@stop