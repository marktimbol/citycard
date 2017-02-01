<?php

namespace App\Jobs;

use Image;
use App\Post;
use Ramsey\Uuid\Uuid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GeneratePostThumbnailPhotos implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $posts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($posts)
    {
        $this->posts = $posts;
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

            $photo_url = Storage::disk('s3')->get($photo->url);
            $thumbnail_28_x_28 = Image::make($photo_url)->fit(28,28)->stream();

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

        return 'Done';
    }
}
