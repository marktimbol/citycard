@extends('layouts.merchant')


@section('content')

	<div class="row">
		<div class="col-md-3">

		</div>
		<div class="col-md-9">
			<h1>Posts</h1>

			<table class="tabel table-bordered table-striped">
				<thead>
					<tr>
						<th>Post Type</th>
						<th>Title</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse($posts as $post)
						<tr>
							<td>{{ ucfirst($post->type) }}</td>
							<td>{{ $post->title }}</td>
							<td>&nbsp;</td>
						</tr>
					@empty
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

@endsection