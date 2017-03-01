@include('merchants.header')

    <div class="container-fluid">
        <div class="row">
            @if( auth()->check() )
                <div class="col-md-3">
                    <h2 class="Heading__title">Hi, {{ auth()->user()->first_name }}</h2>
                    <ul class="list-group">
                        <a href="{{ route('clerk.dashboard') }}" class="list-group-item">Dashboard</a>
                        <a href="http://citycard.me" target="_blank" class="list-group-item">Visit Website</a>
                    </ul>
                </div>
            @endif
            <div class="col-md-9">    
                @yield('content')
            </div>
        </div>
    </div>

@include('merchants.footer')
