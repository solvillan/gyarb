<?php
function isMultiple($count) {
    if ($count > 1 || $count == 0) {
        return "s";
    } else {
        return "";
    }
}
?>
<header class="banner">
    <h1 class="header">{{$title or "Pixturation"}}</h1>
    <h2 class="sub-header">{{$subtitle or "Now with ".\App\Models\User::count()." user".isMultiple(\App\Models\User::count())."!"}}</h2>
</header>