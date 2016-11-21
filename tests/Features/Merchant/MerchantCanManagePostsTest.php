<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MerchantCanManagePostsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->actingAsMerchant();
    }

    public function test_a_merchant_can_view_all_their_posts()
    {
    	$post = factory(App\Post::class)->make([
            'title' => 'Post title',
            'type'  => 'notification',
            'approved' => true,
        ]);
    	$this->merchant->posts()->save($post);

    	$this->visit('/merchants/posts')
            ->see('Notification')
    		->see('Post title');
    }

    public function test_a_merchant_can_add_a_new_post_and_assign_it_to_selected_outlets()
    {
        $this->createOutlet([
            'merchant_id'   => $this->merchant->id
        ]);

        $this->createOutlet([
            'merchant_id'   => $this->merchant->id
        ]);

        $this->visit('/merchants/posts/create')
            ->select('notification', 'type')
            ->select(['1', '2'], 'outlet_ids')
            ->type('The Title', 'title')
            ->type('The description', 'desc')
            ->press('Save')

            ->seeInDatabase('posts', [
                'merchant_id'   => $this->merchant->id,
                'type'  => 'notification',
                'title' => 'The Title',
                'slug'  => 'the-title',
                'desc'=> 'The description',
                'approved'  => 0
            ])

            ->seeInDatabase('outlet_posts', [
                'outlet_id' => 1,
                'post_id'   => 1
            ])

            ->seeInDatabase('outlet_posts', [
                'outlet_id' => 2,
                'post_id'   => 1
            ]);
    }

}
