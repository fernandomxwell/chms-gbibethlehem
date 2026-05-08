@extends('layouts.app')

@section('content')
    <h1>@lang('service_types.create')</h1>

    <form action="{{ route('service_types.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('service_types.form', ['serviceType' => null])

        <button type="submit" class="btn btn-primary">@lang('submit')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection
