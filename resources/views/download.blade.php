@extends('templates.main')

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

@section('content')
@if(isset($_SESSION['token']))
    <ul class="osList center">
        <li><a href="/download/jar"><img class="osLogo" src="styles/img/winlogo.svg"></a></li>
        <li><a href="/download/jar"><img class="osLogo" src="styles/img/osxlogo.svg"></a></li>
        <li><a href="/download/jar"><img class="osLogo" src="styles/img/linuxlogo.svg"></a></li>
    </ul>
@else
    <div class="center">
        <a class="button go" href="/login">Login!</a>
    </div>
@endif
@endsection