<header>
    <div class="Header__content">
        <div class="Logo--container">
            <h1>{{ config('app.name') }}</h1>
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
                    <a href="#">
                        <i class="fa fa-calendar-check-o fa-2x"></i>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-heart-o fa-2x"></i>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-user-o fa-2x"></i>
                    </a>
                </li>                                
            </ul>
        </div>
    </div>
</header>