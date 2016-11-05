@extends('layouts.dashboard')

@section('pageTitle', sprintf('%s Posts', $merchant->name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Posts
			<smal>
				<a href="{{ route('dashboard.merchants.posts.create', $merchant->id) }}" 
					class="btn btn-sm btn-primary"
				>
					Add New
				</a>
			</smal>
		</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Title</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $posts as $post )
			<tr>
				<td>
					<a href="{{ route('dashboard.merchants.posts.show', [$merchant->id, $post->id]) }}">
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