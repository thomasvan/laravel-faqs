@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-centers">
                        <h2>Ask Question</h2>
                        <div class="ml-auto">
                            <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Back to All Questions</a>
                        </div>
                    </div>
                
                </div>

                <div class="card-body">
                    <form action="{{ route('questions.store') }}" method="post">
                        @include('questions._form', ['submitButtonTitle' => 'Ask Question'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
