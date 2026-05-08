@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.create')</h1>

    <form action="{{ route('congregants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('congregants.form', ['congregant' => null])

        <button type="submit" class="btn btn-primary">@lang('submit')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection

@section('javascript')
    @include('congregants.script')
@endsection
