<?php

namespace App\Policies;

use App\User;
use App\Admin;
use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin)
    {
        return $admin->hasRole('admin');
    }

    /**
     * Determine whether the user can view the merchant.
     *
     * @param  \App\User  $user
     * @param  \App\Merchant  $merchant
     * @return mixed
     */
    public function view(User $user, Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can create merchants.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the admin can update the merchant.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Merchant  $merchant
     * @return mixed
     */
    public function update(Admin $admin, Merchant $merchant)
    {
        return $admin->merchants->contains($merchant->id);
    }

    /**
     * Determine whether the admin can delete the merchant.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Merchant  $merchant
     * @return mixed
     */
    public function delete(Admin $admin, Merchant $merchant)
    {
        return $admin->merchants->contains($merchant->id);
    }
}
