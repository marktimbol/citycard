<?php

namespace App\Jobs;

use Image;
use App\Post;
use App\Photo;
use Ramsey\Uuid\Uuid;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneratePostThumbnailPhotos implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Photo::chunk(300, function($photos) {
            foreach( $photos as $photo )
            { 
                $post = Post::with('merchant:id,name')
                    ->whereId($photo->imageable_id)
                    ->select('id', 'merchant_id')
                    ->first();

                $thumbnail_28_x_28 = Image::make(getPhotoPath($photo->url))
                    ->fit(28,28)
                    ->stream();

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
        });

        Log::info('GeneratePostThumbnailPhotos. Done.');
    }
}
