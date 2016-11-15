<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pixturation</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link href="/styles/main.css" rel="stylesheet">
    <style>
        {{--.wrapper::before {--}}
            {{--background-image: url("{{$bg or "http://www.cianellistudios.com/images/abstract-art/abstract-art-mother-earth.jpg"}}");--}}
        {{--}--}}
    </style>
    @yield('styles')
</head>
<body>
<div class="wrapper">
@include('parts.header')

<section class="container">
    @yield('content')
</section>

@include('parts.footer')
@yield('scripts')
</div>
</body>
</html>