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
    protected $user;
    protected $clerk;
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

    protected function adminSignIn($attributes = [])
    {        
        $this->actingAsAdmin($attributes);
    }

    protected function actingAsAdmin($attributes = [])
    {
        $role = factory(App\Role::class)->create([
            'name'  => 'admin'
        ]);

        $this->admin = $this->createAdmin($attributes);
        $this->admin->assignRole('admin');
        
        $this->actingAs($this->admin, 'admin');
    }

    protected function actingAsMerchant()
    {
        $this->merchant = $this->createMerchant();
        $this->actingAs($this->merchant, 'merchant');
    }

    protected function actingAsUser($attributes=[])
    {
        $this->user = $this->createUser($attributes);

        factory(App\Transaction::class)->create([
            'user_id'   => $this->user->id,
            'description'   => 'You received 100 points upon registration.',
            'credit'    => 100,
            'debit' => 0,
            'balance'   => 100,
        ]);

        $this->actingAs($this->user, 'user_api');
    }   

    protected function actingAsClerk($attributes=[])
    {
        $this->clerk = $this->createClerk($attributes);

        $this->actingAs($this->clerk, 'clerk_api');
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

    protected function createReservation($attributes = [])
    {
        return factory(App\Reservation::class)->create($attributes);
    }
    
    protected function createItemForReservation($attributes = [])
    {
        return factory(App\ItemForReservation::class)->create($attributes);
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
        $post = factory(App\Post::class)->create($attributes);

        if( auth()->guard('admin')->check() )
        {
            $user = auth()->guard('admin')->user();
            $user->posts()->attach($post);
        }

        return $post;
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

    protected function createAlbum($attributes = [])
    {
        return factory(App\Album::class)->create($attributes);
    }

    protected function createAlbumPhoto($attributes = [])
    {
        return factory(App\AlbumPhoto::class)->create($attributes);
    }

    protected function createOutletMenu($attributes = [])
    {
        return factory(App\OutletMenu::class)->create($attributes);
    }

    protected function createOutletShopFront($attributes = [])
    {
        return factory(App\ShopFront::class)->create($attributes);
    }
}
