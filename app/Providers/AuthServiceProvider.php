<?php

namespace App\Providers;

use App\Admin;
use App\Clerk;
use App\Merchant;
use App\Outlet;
use App\Faq;
use App\Post;
use App\Permission;
use App\Policies\FaqPolicy;
use App\Policies\ClerkPolicy;
use App\Policies\MerchantPolicy;
use App\Policies\OutletPolicy;
use App\Policies\PostPolicy;
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
        Merchant::class => MerchantPolicy::class,
        Outlet::class => OutletPolicy::class,
        Clerk::class => ClerkPolicy::class,
        Post::class => PostPolicy::class,
        Faq::class => FaqPolicy::class,
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
