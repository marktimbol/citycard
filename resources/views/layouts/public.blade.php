<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', config('app.name', 'Laravel'))</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ elixir('css/public.css') }}" rel="stylesheet">

    @yield('header_styles')
    
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
    <body class="@yield('bodyClass')">

        @yield('content')

        <script src="{{ elixir('js/public.js') }}"></script>
        @include('layouts.public._footer')
    </body>
</html>
