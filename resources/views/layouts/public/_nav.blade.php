<header>
    <div class="Header__content">
        <div class="Logo--container">
            <h1>
                <a href="/">
                    <img src="/images/logo.svg" alt="CityCard" title="CityCard" class="img-responsive" width="175" height="51" />
                </a>
            </h1>
        </div>
        <div class="Search--container">
            <form method="GET">
                <div class="form-group">
                    <input type="text" name="s" class="form-control input-sm" placeholder="Search" />
                </div>
            </form>
        </div>
        <div class="Menus--container">
            <ul>
                <li>
                    <a href="/events" class="citycard-icon icon-calendar">
                        Events
                    </a>
                </li>
                <li>
                    <a href="/explore" class="citycard-icon icon-explore">
                        Explore Merchants
                    </a>
                </li>
                <li>
                    <a href="#notifications" class="citycard-icon icon-heart">
                        Notifications
                    </a>
                </li>
                @if( auth()->check() )
                    <li>
                        <a href="/user/{{ auth()->user()->id }}" class="citycard-icon icon-profile">
                            User Profile
                        </a>
                    </li> 
                @endif                         
            </ul>
        </div>
    </div>
</header>