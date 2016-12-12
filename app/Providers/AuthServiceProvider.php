<?php

namespace App\Providers;

use App\Admin;
use App\Merchant;
use App\Outlet;
use App\Permission;
use App\Policies\MerchantPolicy;
use App\Policies\OutletPolicy;
use App\Policies\PostPolicy;
use App\Post;
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
        Merchant::class => MerchantPolicy::class,
        Outlet::class => OutletPolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        foreach( $this->getPermissions() as $permission ) {
            Gate::define($permission->name, function(Admin $user) use($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
