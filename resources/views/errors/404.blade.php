<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>404 Custom Error Page Example</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>
<div class="container mt-5 pt-5">
    <div class="alert alert-danger text-center">
        <h2 class="display-3">404</h2>
        <p class="display-5">Oops! Something is wrong.</p>
        @if(auth()->active())
            <a href="{{ route('home') }}">Go back home</a>
        @else
            <a href="{{ route('login') }}">Please login</a>
        @endif
    </div>
</div>
</body>

</html>
