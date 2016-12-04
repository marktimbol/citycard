<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    protected $apiUrl = '/api';

    protected $admin;
    protected $merchant;


    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function createAdmin($attributes = [])
    {
        return factory(App\Admin::class)->create($attributes);
    }

    protected function adminSignIn()
    {
        $this->actingAsAdmin();
    }

    protected function actingAsAdmin()
    {
        $this->admin = $this->createAdmin();
        $this->actingAs($this->admin, 'admin');
    }

    protected function actingAsMerchant()
    {
        $this->merchant = $this->createMerchant();
        $this->actingAs($this->merchant, 'merchant');
    }

    protected function actingAsUser($attributes=[])
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user, 'user_api');
    }    

    protected function createUser($attributes = [])
    {
        return factory(App\User::class)->create($attributes);
    }

    protected function createMerchant($attributes = [])
    {
        return factory(App\Merchant::class)->create($attributes);
    }

    protected function createOutlet($attributes = [])
    {
        return factory(App\Outlet::class)->create($attributes);
    }

    protected function createOutlets($count = 2)
    {
        return factory(App\Outlet::class, $count)->create();
    }

    protected function createClerk($attributes = [])
    {
        return factory(App\Clerk::class)->create($attributes);
    }

    protected function makeClerk($attributes = [])
    {
        return factory(App\Clerk::class)->make($attributes);
    }

    protected function createPromo($attributes = [])
    {
        return factory(App\Promo::class)->create($attributes);
    }

    protected function createPost($attributes = [])
    {
        return factory(App\Post::class)->create($attributes);
    }

    protected function createCountry($attributes = [])
    {
        return factory(App\Country::class)->create($attributes);
    }

    protected function createCity($attributes = [])
    {
        return factory(App\City::class)->create($attributes);
    }

    protected function createArea($attributes = [])
    {
        return factory(App\Area::class)->create($attributes);
    }

    protected function createSource($attributes = [])
    {
        return factory(App\Source::class)->create($attributes);
    }

    protected function createCategory($attributes = [])
    {
        return factory(App\Category::class)->create($attributes);
    }

    protected function createSubCategory($attributes = [])
    {
        return factory(App\Subcategory::class)->create($attributes);
    }
}
