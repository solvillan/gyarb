<?php
function isMultiple($count) {
    if ($count > 1 || $count == 0) {
        return "s";
    } else {
        return "";
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!--<header class="banner">
    <h1 class="header">{{$title or "Pixturation"}}</h1>
    <h2 class="sub-header">{{$subtitle or "Now with ".\App\Models\User::count()." user".isMultiple(\App\Models\User::count())."!"}}</h2>
</header>-->

<header class="header">
    @if(!isset($hideLogo))
        <a class="logoContainer-s" href="/">
            <img class="logoImg-s" src="/styles/img/logo.svg">
            <span class="logoTxt-s">Pixturation</span>
        </a>
    @endif
    @if(!isset($_SESSION['token']))
        <a href="/login" class="right">Login</a>
    @else
        <span class="right">{{htmlentities($_SESSION['name'])}}</span>
        <a href="/logout" class="rsep right">Logout</a>
    @endif
</header>
