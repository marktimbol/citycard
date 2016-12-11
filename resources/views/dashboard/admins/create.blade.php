@extends('layouts.dashboard')

@section('pageTitle', 'Create New Admin')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create New Admin</h1>
	</div>
	
	<div class="row">
		<div class="col-md-6">
		    <form role="form" method="POST" action="{{ route('dashboard.admins.store') }}">
		        {{ csrf_field() }}
		        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		            <label for="name" class="control-label">Name</label>
		            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus />
		            @if ($errors->has('name'))
		                <span class="help-block">
		                    <strong>{{ $errors->first('name') }}</strong>
		                </span>
		            @endif
		        </div>

		        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
		            <label for="email" class="control-label">E-Mail Address</label>
	                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required />
	                @if ($errors->has('email'))
	                    <span class="help-block">
	                        <strong>{{ $errors->first('email') }}</strong>
	                    </span>
	                @endif
		        </div>

		        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
		            <label for="password" class="control-label">Password</label>
	                <input id="password" type="password" class="form-control" name="password" required />
	                @if ($errors->has('password'))
	                    <span class="help-block">
	                        <strong>{{ $errors->first('password') }}</strong>
	                    </span>
	                @endif
		        </div>

		        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
		            <label for="password-confirm" class="control-label">Confirm Password</label>
	                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required />
	                @if ($errors->has('password_confirmation'))
	                    <span class="help-block">
	                        <strong>{{ $errors->first('password_confirmation') }}</strong>
	                    </span>
	                @endif
		        </div>

		        <div class="form-group">
	                <button type="submit" class="btn btn-primary">
	                    Save
	                </button>
		        </div>
		    </form>	
	    </div>
    </div>
@endsection
