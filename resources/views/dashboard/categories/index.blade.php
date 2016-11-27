@extends('layouts.dashboard')

@section('pageTitle', 'Categories')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Categories
					<small>
						<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#importCategoriesModal">
							Import from Excel
						</button>
					</small>
				</h1>
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
							{{ $category->name }} &mdash;
							<small>({{ $category->subcategories->count() }} Subcategories)</small>
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

	<!-- Modal -->
	<div class="modal fade" id="importCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Import Categories</h4>
	      		</div>
		    	<div class="modal-body">
					<form class="dropzone" method="POST" action="/dashboard/import/categories">
						{{ csrf_field() }}
					</form>
				</div>
	      		<div class="modal-footer">
	    			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
@endsection
