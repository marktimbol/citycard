<?php

namespace App\Http\Controllers\Dashboard;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublishPostsController extends Controller
{
    public function store(Request $request)
    {
		$posts = Post::whereIn('id', $request->posts)->get();

    	foreach( $posts as $post ) {
    		$post->published = true;
    		$post->save();
    	}

        $posts = Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])
                    ->unpublished()
                    ->latest()
                    ->paginate(20);

        return response()->json([
            'posts' => $posts,
            'message' => 'Selected posts has been successfully published.'
        ]);

    	// flash()->success('Selected posts has been successfully published.');
    	// return back();
    }

    public function publishAll()
    {
        $posts = Post::all();
        foreach( $posts as $post )
        {
            $post->published = true;
            $post->save();
        }

        return 'Done';
    }

    public function destroy(Request $request)
    {
		$posts = Post::whereIn('id', $request->posts)->get();

    	foreach( $posts as $post ) {
    		$post->published = false;
    		$post->save();
    	}

        $posts = Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])
                    ->published()
                    ->latest()
                    ->paginate(20);

        return response()->json([
            'posts' => $posts,
            'message' => 'Selected posts has been successfully unpublished.'
        ]);

    	// flash()->success('Selected posts has been successfully unpublished.');
    	// return back();
    }
}
