@extends('layouts.dashboard')

@section('pageTitle', 'Sources')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Sources</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.sources.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						placeholder="eg. Cobone" />
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
		<div class="col-md-8">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $sources as $source )
					<tr>
						<td width="250">
							<a href="{{ route('dashboard.sources.posts.index', $source->id) }}">
								{{ $source->name }} &mdash; {{ $source->posts->count() }} Posts
							</a>
						</td>
						<td>
							<div class="btn-group">
								<a href="#"
									class="btn btn-sm btn-default"
								>
									Edit
								</a>
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
		</div>
	</div>

@endsection
