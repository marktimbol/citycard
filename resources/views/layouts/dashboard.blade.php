<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', config('app.name', 'Laravel') ) | City Card Dashboard</title>

    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    @yield('header_styles')

    <!-- Scripts -->
    <script>
        window.App = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body class="Dashboard">
    @include('layouts._nav')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <h1>Dashboard</h1>
                <aside>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.index') }}" class="list-group-item">Home</a>
                        <a href="{{ route('dashboard.merchants.index') }}" class="list-group-item">Merchants</a>
                    </ul>
                </aside>
            </div>
            <div class="col-md-9 Content">
                @yield('content')
            </div>
        </div>
    </div>

    <footer></footer>
    
    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>

    @yield('footer_scripts')
    
    @include('dashboard._flash')
    
</body>
</html>
