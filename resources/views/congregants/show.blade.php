@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.show')</h1>

    <div class="form-group mb-3">
        <label class="form-label">@lang('honorific_title'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->honorific_title?->label() ?? '-' }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('full_name'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->full_name }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('gender'):</label>
        <input type="text" readonly class="form-control" value="@lang($congregant->gender)">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('date_of_birth'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->formatted_date_of_birth }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('phone_number'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->phone_number }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('email'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->email }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('date_of_baptism'):</label>
        <input type="text" readonly class="form-control" value="{{ $congregant->formatted_date_of_baptism }}">
    </div>
    <div class="form-group mb-3">
        <label class="form-label">@lang('status'):</label>
        <input type="text" readonly class="form-control" value="@lang($congregant->status)">
    </div>

    <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
@endsection
