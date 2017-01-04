<?php

namespace App\Policies;

use App\Admin;
use App\Faq;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaqPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin)
    {
        return $admin->hasRole('admin');
    }

    public function destroy(Admin $admin, Faq $faq)
    {
        return $admin->hasRole('admin');
    }
}
