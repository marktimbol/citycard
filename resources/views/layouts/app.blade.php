<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CityCard is a unique loyalty program designed to bring more pleasure to your retail and dining experience." />
    <meta name="keywords" content="loyalty program, points, cashback, rewards, vouchers, messaging" />
    <meta name="author" content="Ark Solutions" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div>
        @include('layouts._nav')

        @yield('content')

    </div>

    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
