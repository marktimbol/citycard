@extends('layouts.dashboard')

@section('pageTitle', 'Categories')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Categories</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.categories.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						placeholder="etc. Food" />
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
					@forelse( $categories as $category )
					<tr>
						<td width="250">
							<a href="#">
								{{ $category->name }}
							</a>
						</td>
						<td>
							<div class="btn-group">
								<a href="{{ route('dashboard.categories.subcategories.index', $category->id) }}"
									class="btn btn-sm btn-default"
								>
									Manage Subcategories
								</a>
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
