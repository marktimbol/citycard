@extends('layouts.dashboard')

@section('pageTitle', config('app.name'))

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">{{ config('app.name') }}</h1>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<h3>About {{ config('app.name') }}
				<small>
					<a href="#" data-toggle="modal" data-target="#UpdateAboutCityCard">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
			</h3>
			{!! $company->about or '' !!}
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-md-12">
			<h3>Terms &amp; Conditions
				<small>
					<a href="#" data-toggle="modal" data-target="#UpdateTermsAndConditions">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
			</h3>
			{!! $company->terms or '' !!}
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-md-12">
			<h3>Privacy Policy
				<small>
					<a href="#" data-toggle="modal" data-target="#UpdatePrivacyPolicy">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
			</h3>
			{!! $company->privacy or '' !!}
		</div>
	</div>

	<div class="modal fade" id="UpdateAboutCityCard" tabindex="-1" role="dialog">
		@include('dashboard.company._update-modal', [
			'title'	=> 'About ' . config('app.name'),
			'route'	=> '/dashboard/company/'.$company->id.'/?only=about',
			'content'	=> $company->about,
			'editor'	=> ''
		])
	</div>

	<div class="modal fade" id="UpdateTermsAndConditions" tabindex="-1" role="dialog">
		@include('dashboard.company._update-modal', [
			'title'	=> 'Terms and Conditions',
			'route'	=> '/dashboard/company/'.$company->id.'/?only=terms',
			'content'	=> $company->terms,
			'editor'	=> '2'
		])
	</div>

	<div class="modal fade" id="UpdatePrivacyPolicy" tabindex="-1" role="dialog">
		@include('dashboard.company._update-modal', [
			'title'	=> 'Privacy Policy',
			'route'	=> '/dashboard/company/'.$company->id.'/?only=privacy',
			'content'	=> $company->privacy,
			'editor'	=> '3'
		])
	</div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection
