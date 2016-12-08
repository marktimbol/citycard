@include('layouts.dashboard._header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <h1>Dashboard</h1>
                <aside>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.index') }}" class="list-group-item">Home</a>
                    </ul>
                    <h3>Manage</h3>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.merchants.index') }}" class="list-group-item">
                            Merchants
                        </a>
                        <a href="{{ route('dashboard.countries.index') }}" class="list-group-item">
                            Countries
                        </a>
                        <a href="{{ route('dashboard.categories.index')}}" class="list-group-item">
                            Categories
                        </a>
                        <a href="{{ route('dashboard.sources.index') }}" class="list-group-item">
                            Externals (eg. Groupon)
                        </a>   

                        <a href="{{ route('dashboard.roles.index') }}" class="list-group-item">
                            Roles
                        </a>   
                    </ul>
                    <h3>Registered Users</h3>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.users.index') }}" class="list-group-item">
                            Users
                        </a>    
                    </ul>

                    <h3>System Admins</h3>
                    <ul class="list-group">
                        <a href="{{ route('dashboard.admins.index') }}" class="list-group-item">
                            View All
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
