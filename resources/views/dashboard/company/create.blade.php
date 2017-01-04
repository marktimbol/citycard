@extends('layouts.dashboard')

@section('pageTitle', config('app.name'))

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Company Information</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<form method="POST" action="{{ route('dashboard.company.store') }}">
		{{ csrf_field() }}
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="about">About {{ config('app.name') }}</label>
					<textarea name="about" id="editor"></textarea>
				</div>

				<div class="form-group">
					<label for="about">Terms &amp; Conditions</label>
					<textarea name="terms" id="editor2"></textarea>
				</div>

				<div class="form-group">
					<label for="about">Privacy Policy</label>
					<textarea name="privacy" id="editor3"></textarea>
				</div>

				<div class="form-group">
					<button class="btn btn-primary">Save</button>
				</div>
			</div>
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection
