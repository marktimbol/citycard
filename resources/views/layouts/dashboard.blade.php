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
                        @can('manage_merchants')
                            <a href="{{ route('dashboard.merchants.index') }}" class="list-group-item">
                                Merchants
                            </a>
                        @endcan
                        @can('manage_posts')
                            <a href="{{ route('dashboard.posts.index') }}" class="list-group-item">
                                Posts
                            </a>
                        @endcan                        
                        @can('manage_countries')
                            <a href="{{ route('dashboard.countries.index') }}" class="list-group-item">
                                Countries
                            </a>
                        @endcan
                        @can('manage_categories')
                            <a href="{{ route('dashboard.categories.index')}}" class="list-group-item">
                                Categories
                            </a>
                        @endcan
                        @can('manage_externals')
                            <a href="{{ route('dashboard.sources.index') }}" class="list-group-item">
                                Externals (eg. Groupon)
                            </a>
                        @endcan
                    </ul>

                    @can('manage_users')
                        <h3>Registered Users</h3>
                        <ul class="list-group">
                            <a href="{{ route('dashboard.users.index') }}" class="list-group-item">
                                Users
                            </a>    
                        </ul>
                    @endcan

                    @can('manage_roles')
                        <h3>Access Control List</h3>
                        <ul class="list-group">
                            <a href="{{ route('dashboard.roles.index') }}" class="list-group-item">
                                Roles
                            </a>

                            <a href="{{ route('dashboard.permissions.index') }}" class="list-group-item">
                                Permissions
                            </a>   
                        </ul>
                    @endcan

                    @can('manage_admins')
                        <h3>System Admins</h3>
                        <ul class="list-group">
                            <a href="{{ route('dashboard.admins.index') }}" class="list-group-item">
                                View All
                            </a>    
                        </ul>   
                    @endcan   

                    <ul class="list-group">
                        <a href="{{ url('/logout') }}"
                            class="btn btn-block btn-danger"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                            Logout
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
