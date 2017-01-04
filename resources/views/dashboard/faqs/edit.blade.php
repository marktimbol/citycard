@extends('layouts.dashboard')

@section('pageTitle', 'Frequently Asked Questions')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Edit Question</h1>
			</div>

			<form method="POST" action="{{ route('dashboard.faqs.update', $faq->id) }}">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<div class="form-group">
					<label for="question">Question</label>
					<input
						type="text"
						name="question"
						id="question"
						class="form-control"
						value="{{ $faq->question }}"
						placeholder="How do I get cashback?" />
				</div>

				<div class="form-group">
					<label for="answer">Answer</label>
					<textarea id="editor" name="answer" class="form-group">
						{{ $faq->answer }}
					</textarea>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection
