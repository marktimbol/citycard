<?php

namespace App\CityCard;

use Image;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadedPhoto
{
	/**
	 * Upload the selected file to s3
	 */
	public function upload(UploadedFile $file, $merchant, $post_id)
	{
	    $upload_path = sprintf('merchants/%s/posts/%s', $merchant, $post_id);

	    return $file->store($upload_path, 's3');
	}

	/**
	 * Generate a thumbnail of 28x28 dimensions
	 * to achieve Medium's like image effect.
	 */
	public function createThumbnail(UploadedFile $file, $merchant, $post_id)
	{
        $thumbnail = Image::make($file->getRealPath())
            ->fit(28,28)
            ->stream();

    	$filename = Uuid::uuid1()->toString() . '.jpeg';
        $upload_path = sprintf('merchants/%s/posts/%s/thumbs/%s', $merchant, $post_id, $filename);

        Storage::disk('s3')->put($upload_path, $thumbnail->__toString());

        return $upload_path;
	}
}