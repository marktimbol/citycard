@extends('layouts.dashboard')

@section('pageTitle', sprintf('%s Posts', $source->name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ ucfirst($source->name) }} Posts</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Post Type</th>
				<th>Title</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $posts as $post )
			<tr>
				<td width="100">{{ ucfirst($post->type) }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.posts.show', [$post->merchant->id, $post->id]) }}">
						{{ $post->title }}
					</a>
				</td>
				<td>

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