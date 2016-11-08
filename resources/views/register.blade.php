@extends('templates.main')

@section('scripts')
    <script src="/scripts/register.js"></script>
@endsection

@section('content')
    <form id="regForm" method="post" onsubmit="return register();">
        <label for="name">Username</label>
        <input type="text" name="name" required id="name">
        <label for="email">Email</label>
        <input type="email" name="email" required id="email">
        <label for="password">Password</label>
        <input type="password" name="password" required id="password" >
        <input type="submit" value="Register!">
    </form>
@endsection