@extends('layouts.dashboard')

@section('pageTitle', 'Frequently Asked Questions')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">{{ $faq->question }}
				@can('update', $faq)		
					<small>
						<a href="{{ route('dashboard.faqs.edit', $faq->id) }}">
							<i class="fa fa-pencil"></i>
						</a>
					</small>
				@endcan
				</h1>
			</div>

			{!! $faq->answer !!}
		</div>
	</div>

	@can('destroy', $faq)
		@include('dashboard._delete', [
			'route'	=> route('dashboard.faqs.destroy', $faq->id)
		])
	@endcan

@endsection
