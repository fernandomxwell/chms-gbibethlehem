@extends('layouts.app')

@section('content')
    <h1>@lang('service_types.edit')</h1>

    <form action="{{ route('service_types.update', $serviceType->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('service_types.form')

        <button type="submit" class="btn btn-primary">@lang('update')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection
