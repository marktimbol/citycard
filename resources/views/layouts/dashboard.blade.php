<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', config('app.name', 'Laravel') )</title>

{{--     <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet"> --}}
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

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <h1>Dashboard</h1>
                    <aside>
                        <ul class="list-group">
                            <a href="{{ route('dashboard.index') }}" class="list-group-item">Home</a>
                            <a href="{{ route('dashboard.merchants.index') }}" class="list-group-item">Merchants</a>
                            <a href="#" class="list-group-item">Transactions</a>
                        </ul>
                    </aside>
                </div>
                <div class="col-md-9 Content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>

    @include('dashboard._flash')
    
</body>
</html>
