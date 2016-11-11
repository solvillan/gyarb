@extends('templates.main')

@section('styles')
    <link rel="stylesheet" href="/styles/register.css">
@endsection

@section('scripts')
    <script src="/scripts/register.js"></script>
@endsection

@section('content')
    <form id="regForm" class="center" method="post" onsubmit="return register();">
        <ul>
            <li>
                <label class="label" for="name">Username</label>
                <input class="input" type="text" name="name" required id="name">
            </li>
            <li>
                <label class="label" for="email">Email</label>
                <input class="input" type="email" name="email" required id="email">
            </li>
            <li>
                <label class="label" for="password">Password</label>
                <input class="input" type="password" name="password" required id="password" >
            </li>
            <li>
                <input class="submit button" type="submit" value="Register!">
            </li>
        </ul>
    </form>
@endsection