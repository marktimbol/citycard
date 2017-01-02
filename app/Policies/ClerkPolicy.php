<?php

namespace App\Policies;

use App\User;
use App\Admin;
use App\Clerk;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClerkPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin)
    {
        return $admin->hasRole('admin');
    }
    
    /**
     * Determine whether the user can view the clerk.
     *
     * @param  \App\User  $user
     * @param  \App\Clerk  $clerk
     * @return mixed
     */
    public function view(User $user, Clerk $clerk)
    {
        //
    }

    /**
     * Determine whether the user can create clerks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the clerk.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Clerk  $clerk
     * @return mixed
     */
    public function update(Admin $admin, Clerk $clerk)
    {

    }

    /**
     * Determine whether the user can delete the clerk.
     *
     * @param  \App\User  $user
     * @param  \App\Clerk  $clerk
     * @return mixed
     */
    public function delete(User $user, Clerk $clerk)
    {
        //
    }
}
