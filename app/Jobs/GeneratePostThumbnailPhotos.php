<?php

namespace App\Jobs;

use Image;
use App\Post;
use Ramsey\Uuid\Uuid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePostThumbnailPhotos implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $photos;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($photos)
    {
        $this->photos = $photos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach( $this->photos as $photo )
        {        
            $post = Post::with('merchant:id,name')
                ->whereId($photo->imageable_id)
                ->select('id', 'merchant_id')
                ->first();

            $thumbnail_28_x_28 = Image::make(getPhotoPath($photo->url))->fit(28,28)->stream();

            $filename = sprintf(
                'merchants/%s/posts/%s/thumbs/%s',
                str_slug($post->merchant->name),
                $post->id,
                Uuid::uuid1()->toString() . '.jpeg'
            );

            Storage::disk('s3')->put($filename, $thumbnail_28_x_28->__toString());

            $photo->thumbnail = $filename;
            $photo->save();
        }

        return 'GeneratePostThumbnailPhotos. Done.';
    }
}
