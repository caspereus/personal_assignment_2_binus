@extends('layouts.app-master')

@section('content')
    <div class="bg-light p-5 rounded">
        @auth
        <h1>Dashboard</h1>
        <p class="lead">Dashboard User terdaftar</p>
        @endauth

        @guest
        <h1>Home</h1>
        <p class="lead">Masuk untuk melihat menu dashboard</p>
        @endguest
    </div>
@endsection
