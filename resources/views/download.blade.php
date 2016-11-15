@extends('templates.main')

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

@section('content')
@if(isset($_SESSION['token']))
    <ul class="osList center">
        <li><a href="#win"><img class="osLogo" src="styles/img/winlogo.svg"></a></li>
        <li><a href="#osx"><img class="osLogo" src="styles/img/osxlogo.svg"></a></li>
        <li><a href="#linux"><img class="osLogo" src="styles/img/linuxlogo.svg"></a></li>
    </ul>
@else
    <h1>Not logged in!</h1>
@endif
@endsection