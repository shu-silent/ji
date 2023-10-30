@extends('adminlte::page')

@section('title', 'contents')

@section('content_header')
    <h1>Book Name: {{ $book->name }}</h1>
@stop

@section('content')
    
    <button id="add-item-button" class="btn btn-default">+</button>  
    
    @if(session('relatedItems'))
    <ul>
        @foreach(session('relatedItems') as $item)
            <li>
                URL: <a href="{{ $item->url }}">{{ $item->url }}</a>
                <div>
                    OGP Title: {{ $item->ogp['title'] }}
                    OGP Description: {{ $item->ogp['description'] }}
                    OGP Image: <img src="{{ $item->ogp['image'] }}" alt="OGP Image">
                </div>
            </li>
        @endforeach
    </ul>
    @else
        <p>No related items found.</p>
    @endif

    <!-- ポップアップのHTML -->
    <div id="popup" class="card card-primary" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; width: 600px;">
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