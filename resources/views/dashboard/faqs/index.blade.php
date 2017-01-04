@extends('layouts.dashboard')

@section('pageTitle', 'Frequently Asked Questions')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Frequently Asked Questions</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.faqs.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="question">Question</label>
					<input
						type="text"
						name="question"
						id="question"
						class="form-control"
						value="{{ old('question') }}"
						placeholder="How do I get cashback?" />
				</div>

				<div class="form-group">
					<label for="answer">Answer</label>
					<textarea id="editor" name="answer" class="form-group"></textarea>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
		<div class="col-md-6">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Question</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $faqs as $faq )
					<tr>
						<td>
							<a href="{{ route('dashboard.faqs.show', $faq->id) }}">
								{{ $faq->question }}
							</a>
						</td>					
					</tr>
					@empty
					<tr>
						<td colspan="3">No record yet.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection
