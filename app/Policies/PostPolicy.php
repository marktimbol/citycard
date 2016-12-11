<?php

namespace App\Policies;

use App\Admin;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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

    public function edit(Admin $admin, Post $post)
    {
        return $admin->posts->contains($post);
    }

    public function delete(Admin $admin, Post $post)
    {
        return $admin->posts->contains($post);
    }
}
