<?php
$bg = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiDJExJ43k-glbord_HbR8GS70kJD6kmuuPIggEaFHyFB9EyCM";
$hideLogo = true;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

@extends('templates.main')

@section('content')
    <div class="center">
        @include('parts.logo')
        @if(isset($_SESSION['token']))
            <a href="/download" class="button go">Download!</a>
        @else
            <a href="/register" class="button go">Join the Game!</a>
        @endif
    </div>
@endsection