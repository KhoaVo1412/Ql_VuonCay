@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chào mừng {{ Auth::user()->name }}</h2>
    <p>Bạn muốn vào trang nào?</p>

    <form action="{{ route('select.redirect') }}" method="POST">
        @csrf
        <button type="submit" name="redirect" value="admin" class="btn btn-primary">Vào trang Quản lý Admin</button>
        <button type="submit" name="redirect" value="user" class="btn btn-secondary">Vào trang Truy xuất</button>
    </form>
</div>
@endsection