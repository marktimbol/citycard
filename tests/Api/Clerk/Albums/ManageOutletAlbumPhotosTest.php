<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageOutletAlbumPhotosTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Clerk'
		]);
	}

    public function test_an_authorized_clerk_can_view_all_outlet_album_photos()
    {
        $outlet = $this->createOutlet([
        	'name'	=> 'Starbucks'
        ]);

        $album = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'January Photos Album'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $album->id,
        	'url'	=> 'http://facebook.com'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $album->id,
        	'url'	=> 'http://google.com'
        ]);

        $endpoint = sprintf('/api/clerk/albums/%s/photos', $album->id);
        $request = $this->get($endpoint);
        
        $this->seeJson([
        	'url'	=> 'http://facebook.com',
        ]);

        $this->seeJson([
        	'url'	=> 'http://google.com',
        ]);
    }

    public function test_an_authorized_clerk_can_upload_album_photos()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'Al Shaya'
    	]);

        $outlet = $this->createOutlet([
        	'merchant_id'	=> $merchant->id,
        	'name'	=> 'Starbucks'
        ]);

        $album = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'January Photos Album'
        ]);

        $endpoint = sprintf('/api/clerk/albums/%s/photos', $album->id, [
        	'file'	=> 'http://google.com'
        ]);

        $request = $this->post($endpoint);
    }

    public function test_an_authorized_clerk_can_delete_a_photo_from_the_album()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'Al Shaya'
    	]);

        $outlet = $this->createOutlet([
        	'merchant_id'	=> $merchant->id,
        	'name'	=> 'Starbucks'
        ]);

        $album = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'January Photos Album'
        ]);

        $photo = $this->createAlbumPhoto([
        	'album_id'	=> $album->id,
        	'url'	=> 'http://google.com/image.jpg',
        ]);

        $endpoint = sprintf('/api/clerk/albums/%s/photos/%s', $album->id, $photo->id);

        $request = $this->delete($endpoint);

        $this->dontSeeInDatabase('album_photos', [
        	'album_id'	=> $album->id,
        	'url'	=> 'http://google.com/image.jpg'
        ]);

        $this->seeJson([
        	'success'	=> true,
        ]);
    }    
}
