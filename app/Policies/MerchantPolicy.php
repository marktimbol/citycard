<?php

namespace App\Policies;

use App\Admin;
use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
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

    public function edit(Admin $admin, Merchant $merchant)
    {
        return auth()->guard('admin')->user()->merchants->contains($merchant);
    }

    public function delete(Admin $admin, Merchant $merchant)
    {
        return auth()->guard('admin')->user()->merchants->contains($merchant);
    }

}
