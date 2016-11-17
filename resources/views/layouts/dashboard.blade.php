@include('layouts.dashboard._header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <h1>Dashboard</h1>
                <aside>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.index') }}" class="list-group-item">Home</a>
                        <a href="{{ route('dashboard.merchants.index') }}" class="list-group-item">
                            Manage Merchants
                        </a>
                        <a href="{{ route('dashboard.countries.index') }}" class="list-group-item">
                            Manage Countries
                        </a>
                    </ul>
                </aside>
            </div>
            <div class="col-md-9 Content">
                @yield('content')
            </div>
        </div>
    </div>
@include('layouts.dashboard._footer')
