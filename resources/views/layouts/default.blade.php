<!doctype html>
<html class="no-js" lang="us-en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ app_name() }}</title>

        <link rel="stylesheet" href="{{ css('app') }}">

        @stack('styles')
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div id="app">@yield('app')</div>

        @stack('scripts')
    </body>
</html>