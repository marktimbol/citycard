@extends('layouts.dashboard')

@section('pageTitle', 'Admins')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Admins
			<smal>
				<a href="{{ route('dashboard.admins.create') }}" class="btn btn-sm btn-primary">Add New</a>
			</smal>
		</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>eMail</th>
				<th>Roles</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $admins as $admin )
			<tr>
				<td width="200">
					<a href="{{ route('dashboard.admins.show', $admin->id) }}">
						{{ $admin->name }}
					</a>
				</td>
				<td>{{ $admin->email }}</td>
				<td>
					@foreach($admin->roles as $role)
						<label class="label label-success">{{ $role->label }}</label>
					@endforeach
				</td>
				<td>
					<button 
						class="btn btn-sm btn-primary" 
						data-toggle="modal" 
						data-target="#attachRolesToAdmin{{$admin->id}}"
					>
						Attach Roles
					</button>					
					<a href="{{ route('dashboard.admins.edit', $admin->id) }}"
						class="btn btn-sm btn-default"
					>
						Edit
					</a>
				</td>
			</tr>

			<div 
				class="modal fade" 
				id="attachRolesToAdmin{{$admin->id}}" 
				tabindex="-1" 
				role="dialog"
			>
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Attach Roles to {{ $admin->name }}</h4>
						</div>
							<div class="modal-body">
								<form method="POST" action="{{ route('dashboard.admins.roles.store', $admin->id) }}" id="attachRolesToAdmin{{$admin->id}}" name="attachRolesToAdmin{{$admin->id}}">
									{{ csrf_field() }}									
									@foreach( $roles as $role )
									<div class="checkbox">
										<label>
											<input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ auth()->guard('admin')->user()->hasRole($role->name) ? 'checked' : '' }}/>
											{{ $role->label }}
										</label>
									</div>
									@endforeach

									<button type="submit" class="btn btn-primary">
										Attach Roles
									</button>											
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">
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

@endsection
