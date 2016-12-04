@extends('layouts.dashboard')

@section('pageTitle', 'All Posts')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Posts</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Post Type</th>
				<th>Title</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $posts as $post )
			<tr>
				<td width="100">{{ ucfirst($post->type) }}</td>
				<td>
					<a href="{{ route('dashboard.posts.show', $post->id) }}">
						{{ $post->title }}
					</a>
				</td>
			</tr>

			@empty
			<tr>
				<td colspan="6">No record yet.</td>
			</tr>
			@endforelse
		</tbody>
	</table>
@endsection