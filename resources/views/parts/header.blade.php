<?php
function isMultiple($count) {
    if ($count > 1 || $count == 0) {
        return "s";
    } else {
        return "";
    }
}

session_start();

?>
<!--<header class="banner">
    <h1 class="header">{{$title or "Pixturation"}}</h1>
    <h2 class="sub-header">{{$subtitle or "Now with ".\App\Models\User::count()." user".isMultiple(\App\Models\User::count())."!"}}</h2>
</header>-->

<header class="header">
    @if(!isset($_SESSION['token']))
        <a href="/login" class="right">Login</a>
    @else
        <span class="right rsep">{{$_SESSION['name']}}</span>
        <a href="/logout" class="right">Logout</a>
    @endif
</header>
