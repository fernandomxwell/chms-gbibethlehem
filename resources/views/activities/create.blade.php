@extends('layouts.app')

@section('content')
    <h1>@lang('activities.create')</h1>

    <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('activities.form', ['activity' => null])

        <button type="submit" class="btn btn-primary">@lang('submit')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection

@section('javascript')
    <script>
        function toggleRecurrenceFields() {
            const frequency = document.getElementById('frequency').value;
            const intervalGroup = document.getElementById('interval-group');
            const weeklyDays = document.getElementById('weekly-days');
            const endConditionGroup = document.getElementById('end-condition-group');
            const endDate = document.getElementById('end-date');
            const endOccurrences = document.getElementById('end-occurrences');

            if (frequency === 'NONE') {
                intervalGroup.style.display = 'none';
                weeklyDays.style.display = 'none';
                endConditionGroup.style.display = 'none';
                endDate.style.display = 'none';
                endOccurrences.style.display = 'none';
            } else {
                intervalGroup.style.display = 'block';
                endConditionGroup.style.display = 'block';

                if (frequency === 'WEEKLY') {
                    weeklyDays.style.display = 'block';
                } else {
                    weeklyDays.style.display = 'none';
                }
            }
        }

        function toggleEndConditionFields() {
            const endCondition = document.getElementById('end_condition').value;
            const endDate = document.getElementById('end-date');
            const endOccurrences = document.getElementById('end-occurrences');

            if (endCondition === 'on_date') {
                endDate.style.display = 'block';
                endOccurrences.style.display = 'none';
            } else if (endCondition === 'after_occurrences') {
                endDate.style.display = 'none';
                endOccurrences.style.display = 'block';
            } else {
                endDate.style.display = 'none';
                endOccurrences.style.display = 'none';
            }
        }

        document.getElementById('frequency').addEventListener('change', toggleRecurrenceFields);
        document.getElementById('end_condition').addEventListener('change', toggleEndConditionFields);

        toggleRecurrenceFields();
        toggleEndConditionFields();
    </script>
@endsection
