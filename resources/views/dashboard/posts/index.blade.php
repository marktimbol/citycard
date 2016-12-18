@extends('layouts.dashboard')

@section('pageTitle', 'All Posts')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Posts</h1>
		@include('dashboard._search-form')
	</div>

	<div class="btn-group">
		<p>
			<a href="/dashboard/posts?view=published" class="btn btn-sm btn-link">
				Published
			</a>
			|
			<a href="/dashboard/posts?view=for-review" class="btn btn-sm btn-link">
				For Review
			</a>
		</p>
	</div>

	<div id="DashboardPosts"></div>

	<?php /*
	<form method="POST" action="{{ route('dashboard.publish.posts') }}">
		{{ csrf_field() }}
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Title</th>
				</tr>
			</thead>
			<tbody>
				@forelse( $posts as $post )
				<tr>
					<td width="100">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="posts[]" value="{{ $post->id }}" />
								<a href="{{ route('dashboard.merchants.posts.show', [$post->merchant->id, $post->id]) }}">
									{{ ucfirst($post->title) }}
								</a>
							</label>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="6">No record yet.</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</form>
	*/ ?>

	{{ $posts->links() }}
@endsection

@section('footer_scripts')
	<script src="/js/DashboardPosts.js"></script>
@endsection
