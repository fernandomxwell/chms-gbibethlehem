@extends('layouts.app')

@section('content')
    <h1>@lang('roles.edit')</h1>

    @include('layouts.error')

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('roles.form')

        <button type="submit" class="btn btn-primary">@lang('update')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection
