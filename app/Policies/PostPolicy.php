<?php

namespace App\Policies;

use App\Admin;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin)
    {
        return $admin->hasRole('admin');
    }    

    public function edit(Admin $admin, Post $post)
    {
        return $admin->posts->contains($post);
    }

    public function delete(Admin $admin, Post $post)
    {
        return $admin->posts->contains($post);
    }
}
