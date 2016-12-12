<?php

namespace App\Policies;

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

    public function edit(Admin $admin, Merchant $merchant)
    {
        return $admin->merchants->contains($merchant->id);
    }

    public function delete(Admin $admin, Merchant $merchant)
    {
        return $admin->merchants->contains($merchant);
    }

}
