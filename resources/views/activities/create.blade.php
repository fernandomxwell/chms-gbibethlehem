@extends('layouts.app')

@section('content')
    <h1>@lang('activities.create')</h1>

    <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">@lang('name'):</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">@lang('description'):</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description')}}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">@lang('start_time') <small class="text-muted">(@lang('fill_in_the_next_nearest_schedule'))</small>:</label>
            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" class="form-control @error('start_time') is-invalid @enderror" min="{{ now()->format('Y-m-d H:i') }}" required>
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="accordion mb-3">
            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                    <button class="accordion-button bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        @lang('recurrence_rule')
                    </button>
                </h2>
                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                    <div class="accordion-body">
                        <div id="recurrence-builder">
                            <div class="mb-3">
                                <label for="frequency" class="form-label">@lang('frequency'):</label>
                                <select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                    <option value="NONE" {{ old('frequency') == 'NONE' ? 'selected' : '' }}>@lang('none')</option>
                                    <option value="DAILY" {{ old('frequency') == 'DAILY' ? 'selected' : '' }}>@lang('daily')</option>
                                    <option value="WEEKLY" {{ old('frequency') == 'WEEKLY' ? 'selected' : '' }}>@lang('weekly')</option>
                                    <option value="MONTHLY" {{ old('frequency') == 'MONTHLY' ? 'selected' : '' }}>@lang('monthly')</option>
                                    <option value="YEARLY" {{ old('frequency') == 'YEARLY' ? 'selected' : '' }}>@lang('yearly')</option>
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="interval-group">
                                <label for="interval" class="form-label">@lang('interval'):</label>
                                <input type="number" class="form-control @error('interval') is-invalid @enderror" id="interval" name="interval" value="{{ old('interval') ?? '1' }}" min="1">
                                @error('interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="weekly-days" style="display:none;">
                                <span>@lang('days'):</span><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="monday" name="byday[]" value="MO" {{ old('byday') !== null ? (in_array('MO', old('byday')) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="monday">@lang('day.monday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="tuesday" name="byday[]" value="TU" {{ old('byday') !== null ? (in_array('TU', old('byday')) ? 'checked' : '') : '' }} >
                                    <label class="form-check-label" for="tuesday">@lang('day.tuesday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="wednesday" name="byday[]" value="WE" {{ old('byday') !== null ? (in_array('WE', old('byday')) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="wednesday">@lang('day.wednesday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="thursday" name="byday[]" value="TH" {{ old('byday') !== null ? (in_array('TH', old('byday')) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="thursday">@lang('day.thursday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="friday" name="byday[]" value="FR" {{ old('byday') !== null ? (in_array('FR', old('byday')) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="friday">@lang('day.friday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="saturday" name="byday[]" value="SA" {{ old('byday') !== null ? (in_array('SA', old('byday')) ? 'checked' : ''): '' }}>
                                    <label class="form-check-label" for="saturday">@lang('day.saturday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="sunday" name="byday[]" value="SU" {{ old('byday') !== null ? (in_array('SU', old('byday')) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="sunday">@lang('day.sunday')</label>
                                </div>
                                @error('byday')
                                    <div class="small text-danger">{{ $message }}</div>
                                @enderror
                                @error('byday.*')
                                    <div class="small text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="end-condition-group">
                                <label for="end_condition" class="form-label">@lang('end_condition'):</label>
                                <select class="form-control @error('end_condition') is-invalid @enderror" id="end_condition" name="end_condition">
                                    <option value="never" {{ old('end_condition') == 'never' ? 'selected' : '' }}>@lang('never')</option>
                                    <option value="on_date" {{ old('end_condition') == 'on_date' ? 'selected' : '' }}>@lang('on_date')</option>
                                    <option value="after_occurrences" {{ old('end_condition') == 'after_occurrences' ? 'selected' : '' }}>@lang('after_occurrences')</option>
                                </select>
                                @error('end_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="end-date" style="display:none;">
                                <label for="until" class="form-label">@lang('until'):</label>
                                <input type="date" class="form-control @error('until') is-invalid @enderror" id="until" name="until" value="{{ old('until') }}" min="{{ now()->format('Y-m-d') }}">
                                @error('until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="end-occurrences" style="display:none;">
                                <label for="count" class="form-label">@lang('count'):</label>
                                <input type="number" class="form-control @error('count') is-invalid @enderror" id="count" name="count" value="{{ old('count') ?? '1' }}" min="1">
                                @error('count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        // Initialize the form based on old input
        toggleRecurrenceFields();
        toggleEndConditionFields();
    </script>
@endsection
