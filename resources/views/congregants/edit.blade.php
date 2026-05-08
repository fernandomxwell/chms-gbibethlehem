@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.edit')</h1>

    <form action="{{ route('congregants.update', $congregant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('congregants.form')

        <button type="submit" class="btn btn-primary">@lang('update')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection

@section('javascript')
    @include('congregants.script')
@endsection
