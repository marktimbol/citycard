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
        'App\Merchant' => 'App\Policies\MerchantPolicy',
        'App\Outlet' => 'App\Policies\OutletPolicy',
        'App\Clerk' => 'App\Policies\ClerkPolicy',
        'App\Post' => 'App\Policies\PostPolicy',
        'App\Faq' => 'App\Policies\FaqPolicy',
        'App\Reservation' => 'App\Policies\UserReservationPolicy',
        'App\OutletReservation' => 'App\Policies\OutletReservationPolicy',
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
