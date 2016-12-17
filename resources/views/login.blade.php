@extends('templates.main')
<?php
$bg = "http://cdn.wallpapersafari.com/36/26/StsYJw.png";
?>
@section('styles')
    <link rel="stylesheet" href="/styles/register.css">
@endsection

@section('scripts')
    <script src="/scripts/register.js"></script>
@endsection

@section('content')
    <form id="regForm" class="center" method="post" onsubmit="return login();">
        <ul>
            <li>
                <input placeholder="Email" class="input" type="email" name="email" required id="email">
            </li>
            <li>
                <input placeholder="Password" class="input" type="password" name="password" required id="password" >
            </li>
            <li>
                <input class="submit button" type="submit" value="Login!">
            </li>
        </ul>
    </form>
@endsection