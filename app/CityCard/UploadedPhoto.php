<?php

namespace App\CityCard;

use Image;
use App\Post;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadedPhoto
{
	protected $post;
	protected $file_path;
	protected $thumbnail_path;
	protected $uploadedFile;

	/**
	 * Upload the selected file to s3
	 */
	public function upload(UploadedFile $file, Post $post)
	{
		$this->uploadedFile = $file;
		$this->post = $post->load('merchant');

		$merchant = str_slug($this->post->merchant->name);
	    $upload_path = sprintf('merchants/%s/posts/%s', $merchant, $this->post->id);
	    $this->file_path = $this->uploadedFile->store($upload_path, 's3');

	    return $this;
	}

	/**
	 * Generate a thumbnail of 28x28 dimensions
	 * to achieve Medium's like image effect.
	 */
	public function createThumbnail()
	{
        $thumbnail = Image::make($this->uploadedFile->getRealPath())
            ->fit(28,28)
            ->stream();

        $merchant = str_slug($this->post->merchant->name);
    	$thumbnail_filename = Uuid::uuid1()->toString() . '.jpeg';
        $this->thumbnail_path = sprintf('merchants/%s/posts/%s/thumbs/%s', $merchant, $this->post->id, $thumbnail_filename);

        Storage::disk('s3')->put($this->thumbnail_path, $thumbnail->__toString());

        return $this;
	}

	public function save()
	{
        return $this->post->photos()->create([
            'url'   => $this->file_path,
            'thumbnail' => $this->thumbnail_path,
        ]);		
	}
}