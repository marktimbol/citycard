<?php

namespace App\Providers;

use App\Admin;
use App\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        App\Merchant::class => App\Policies\MerchantPolicy::class,
        App\Outlet::class => App\Policies\OutletPolicy::class,
        App\Post::class => App\Policies\PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // foreach( $this->getPermissions() as $permission ) {
        //     Gate::define($permission->name, function(Admin $user) use($permission) {
        //         return $user->hasPermission($permission);
        //     });
        // }
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
