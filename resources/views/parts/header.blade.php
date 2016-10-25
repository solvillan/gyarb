<?php
function isMultiple($count) {
    if ($count > 1 || $count == 0) {
        return "s";
    } else {
        return "";
    }
}
?>
<header>
    <h1>{{$title or "Pixturation"}}</h1>
    <h2>{{$subtitle or "Now with ".\App\Models\User::count()." user".isMultiple(\App\Models\User::count())."!"}}</h2>
</header>