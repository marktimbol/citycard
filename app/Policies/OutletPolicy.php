<?php

namespace App\Policies;

use App\Admin;
use App\Outlet;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutletPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(Admin $admin, Outlet $outlet)
    {
        return $admin->outlets->contains($outlet);
    }

    public function delete(Admin $admin, Outlet $outlet)
    {
        return $admin->outlets->contains($outlet);
    }    
}
