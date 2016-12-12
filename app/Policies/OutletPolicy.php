<?php

namespace App\Policies;

use App\User;
use App\Admin;
use App\Outlet;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutletPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin)
    {
        return $admin->hasRole('admin');
    }

    /**
     * Determine whether the user can view the outlet.
     *
     * @param  \App\User  $user
     * @param  \App\Outlet  $outlet
     * @return mixed
     */
    public function view(User $user, Outlet $outlet)
    {
        //
    }

    /**
     * Determine whether the user can create outlets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the admin can update the outlet.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Outlet  $outlet
     * @return mixed
     */
    public function update(Admin $admin, Outlet $outlet)
    {
        return $admin->outlets->contains($outlet);
    }

    /**
     * Determine whether the admin can delete the outlet.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Outlet  $outlet
     * @return mixed
     */
    public function delete(Admin $admin, Outlet $outlet)
    {
        return $admin->outlets->contains($outlet);
    } 
}
