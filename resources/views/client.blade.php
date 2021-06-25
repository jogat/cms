<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('css/client.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <example/>
        </div>

        <script src="{{ asset('js/app.js') }}" ></script>
    </body>
</html>
