<?php

namespace App\Providers;

use App\Admin;
use App\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        App\Clerk::class => App\Policies\ClerkPolicy::class,
        App\Post::class => App\Policies\PostPolicy::class,
        App\Faq::class => App\Policies\FaqPolicy::class,
        App\UserReservation::class => App\Policies\UserReservationPolicy::class,
        App\OutletReservation::class => App\Policies\OutletReservationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if( permissionIsEnabled() )
        {        
            foreach( $this->getPermissions() as $permission ) {
                Gate::define($permission->name, function(Admin $user) use($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
