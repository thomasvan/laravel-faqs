@extends('layouts.app') @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">All Questions</div>

                <div class="card-body">
                    @foreach ($questions as $question)
                    <div class="media">
                        <div class="d-flex flex-column counters">
                            <div class="vote">
                                <strong>{{ $question->votes }}</strong> {{ str_plural('vote',$question->votes) }}
                            </div>
                            <div class="status {{ $question->status }}">
                                <strong>{{ $question->answers }}</strong> {{ str_plural('answers',$question->answers) }}
                            </div>
                            <div class="view">
                                {{ $question->views . ' ' . str_plural('views',$question->views) }}
                            </div>
                        </div>
                        <div class="media-body">
                            <h3 class="mt-0">
                                <a href="{{ $question->url }}">{{ $question->title }}</a>
                                <p class="lead">
                                    Asked by <a href="{{ $question->user->url }}">{{ $question->user->name }}</a>
                                    <small class="text-muted">{{ $question->created_date }}</small>
                                </p>
                            </h3>
                            {{ str_limit($question->body,250) }}
                        </div>
                    </div>
                    <hr />
                    @endforeach
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
