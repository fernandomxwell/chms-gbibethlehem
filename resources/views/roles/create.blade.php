@extends('layouts.app')

@section('content')
    <h1>@lang('roles.create')</h1>

    @include('layouts.error')

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        @include('roles.form', ['role' => null])

        <button type="submit" class="btn btn-primary">@lang('submit')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection
