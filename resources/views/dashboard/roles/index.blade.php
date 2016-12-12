@extends('layouts.dashboard')

@section('pageTitle', 'Roles')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Roles</h1>
		@include('dashboard._search-form')
	</div>

	<div class="row">
		<div class="col-md-3">
			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.roles.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						value="{{ old('name') }}"
						placeholder="administrator, encoder, etc." />
				</div>	

				<div class="form-group">
					<label for="label">Label</label>
					<input
						type="text"
						name="label"
						id="label"
						class="form-control"
						value="{{ old('label') }}"
						placeholder="Site Administrator, Site Encoder, etc." />
				</div>	

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
		<div class="col-md-9">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>Label</th>
						<th>Permissions</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $roles as $role )
					<tr>
						<td>{{ $role->name }}</td>
						<td>{{ $role->label }}</td>
						<td>
							@foreach( $role->permissions as $permission )
								<label class="label label-success">{{ $permission->name }}</label>
							@endforeach
						</td>
						<td>
							<button 
								class="btn btn-sm btn-primary" 
								data-toggle="modal" 
								data-target="#attachPermissionsToRole{{$role->id}}"
							>
								Attach Permissions
							</button>
							<a href="#" class="btn btn-sm btn-default">
								Edit
							</a>
						</td>
					</tr>
					<div 
						class="modal fade" 
						id="attachPermissionsToRole{{$role->id}}" 
						tabindex="-1" 
						role="dialog"
					>
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Attach Permissions to {{ $role->label }}</h4>
								</div>
									<div class="modal-body">
										<form method="POST" action="{{ route('dashboard.roles.permissions.store', $role->id) }}">
											{{ csrf_field() }}									
											@foreach( $permissions as $permission )
											<div class="checkbox">
												<label>
													<input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ auth()->guard('admin')->user()->hasPermission($permission) ? 'checked' : '' }}/>
													{{ $permission->label }}

												</label>
											</div>

											@endforeach

											<button type="submit" class="btn btn-sm btn-primary">
												Attach Permission
											</button>											
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
											Close
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>	
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
