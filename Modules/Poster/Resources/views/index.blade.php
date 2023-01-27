@extends('poster::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('poster.name') !!}
    </p>
@endsection
