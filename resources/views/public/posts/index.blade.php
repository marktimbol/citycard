@extends('layouts.public')

@section('bodyClass', 'Posts')

@section('content')
	@include('layouts._nav')

	<div class="Posts--container">
		<div class="Posts max-600">
			@foreach( $posts as $post )
				<div class="Card">
					<div class="Card__header">
						<div class="Card__outlet">
							<img src="/images/outlet-avatar.jpg" alt="" title="" class="img-responsive" />
							<a href="#">{{ $post->title }}</a>
						</div>
						<div class="Card__time">
							{{ $post->created_at->diffForHumans() }}
						</div>
					</div>
					<div class="Card__image">
						<img src="http://placehold.it/600x500" alt="" title="" class="img-responsive" />
					</div>
				</div>
			@endforeach
		</div>
	</div>

@endsection