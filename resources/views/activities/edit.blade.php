@extends('layouts.app')

@section('content')
    <h1>@lang('activities.edit')</h1>

    <form action="{{ route('activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('activities.form')

        <button type="submit" class="btn btn-primary">@lang('update')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            const toggleEndConditionFields = () => {
                let endCondition = $('#end_condition').val();

                if (endCondition === 'on_date') {
                    $('#end-date').show();
                    $('#end-occurrences').hide();
                } else if (endCondition === 'after_occurrences') {
                    $('#end-date').hide();
                    $('#end-occurrences').show();
                } else {
                    $('#end-date').hide();
                    $('#end-occurrences').hide();
                }
            }

            const toggleDaysFields = () => {
                let frequency = $('#frequency').val();

                if (frequency !== 'NONE') {
                    $('.recurrence-fields').show();

                    if (frequency === 'WEEKLY') {
                        $('#weekly-days').show();
                    } else {
                        $('#weekly-days').hide();
                    }

                    toggleEndConditionFields();
                } else {
                    $('.recurrence-fields').hide();
                }
            }

            toggleDaysFields();

            $('#frequency').change(function() {
                toggleDaysFields();
            });

            $('#end_condition').change(function() {
                toggleEndConditionFields();
            });

            $('#start_time').change(function () {
                const datetimeValue = $(this).val();
                const dateOnly = datetimeValue.split('T')[0];
                $('#until').attr('min', dateOnly);
            });
        });
    </script>
@endsection
