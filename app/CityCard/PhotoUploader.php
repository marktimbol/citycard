<?php

namespace App\CityCard;

use Image;
use App\Reward;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoUploader
{
	protected $reward;
	// The file that we will upload
	protected $file;
	// The path where we will store the $file
	protected $upload_path;
	// The absolute path of the uploaded file in S3
	protected $file_path;
	// The absolute path of the thumb uploaded file in S3
	protected $thumb_path;

	public function upload(Reward $reward, UploadedFile $file, $upload_path)
	{
		$this->reward = $reward;
		$this->file = $file;
		$this->upload_path = $upload_path;

	    $this->file_path = $file->store($this->upload_path, 's3');

	    return $this;
	}

	/**
	 * Generate a thumbnail of 28x28 dimensions
	 * to achieve Medium's like image effect.
	 */
	public function createThumbnail()
	{
        $thumb = Image::make($this->file->getRealPath())
            ->fit(28,28)
            ->stream();

    	$thumbnail_filename = Uuid::uuid1()->toString() . '.jpeg';
        $this->thumb_path = sprintf('%s/thumbs/%s', $this->upload_path, $thumbnail_filename);

        Storage::disk('s3')->put($this->thumb_path, $thumb->__toString());

        return $this;
	}

	public function save()
	{
        return $this->reward->photos()->create([
            'url'   => $this->file_path,
            'thumbnail' => $this->thumb_path,
        ]);	
	}
}