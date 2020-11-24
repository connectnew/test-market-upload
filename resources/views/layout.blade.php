<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    @stack('styles')

</head>

<body class="bg-light">

    <div id="app-vue">

        <vue-progress-bar></vue-progress-bar>
        <Core></Core>

        <div class="container">
            @yield('content')
        </div>

        <footer class="my-5 pt-5 text-muted text-center text-small">
        </footer>
    </div>

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>
