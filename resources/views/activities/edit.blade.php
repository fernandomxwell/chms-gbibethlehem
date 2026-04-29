@extends('layouts.app')

@section('content')
    <h1>@lang('activities.edit')</h1>

    <form action="{{ route('activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">@lang('name'):</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $activity->name) }}" maxlength="100" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">@lang('description'):</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $activity->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">@lang('start_time') <small class="text-muted">(@lang('fill_in_the_next_nearest_schedule'))</small>:</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $activity->start_time->format('Y-m-d H:i')) }}" required>
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="accordion" id="accordionPanelsStayOpenExample">
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
                                    <option value="NONE" {{ old('frequency', $frequency) == 'NONE' ? 'selected' : '' }}>@lang('none')</option>
                                    <option value="DAILY" {{ old('frequency', $frequency) == 'DAILY' ? 'selected' : '' }}>@lang('daily')</option>
                                    <option value="WEEKLY" {{ old('frequency', $frequency) == 'WEEKLY' ? 'selected' : '' }}>@lang('weekly')</option>
                                    <option value="MONTHLY" {{ old('frequency', $frequency) == 'MONTHLY' ? 'selected' : '' }}>@lang('monthly')</option>
                                    <option value="YEARLY" {{ old('frequency', $frequency) == 'YEARLY' ? 'selected' : '' }}>@lang('yearly')</option>
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="interval-group">
                                <label for="interval" class="form-label">@lang('interval'):</label>
                                <input type="number" class="form-control @error('interval') is-invalid @enderror" id="interval" name="interval" value="{{ old('interval', $interval) ?? '1' }}" min="1">
                                @error('interval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="weekly-days" style="display:none;">
                                <span>@lang('days'):</span><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="monday" name="byday[]" value="MO" {{ in_array('MO', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="monday">@lang('day.monday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="tuesday" name="byday[]" value="TU" {{ in_array('TU', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tuesday">@lang('day.tuesday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="wednesday" name="byday[]" value="WE" {{ in_array('WE', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wednesday">@lang('day.wednesday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="thursday" name="byday[]" value="TH" {{ in_array('TH', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="thursday">@lang('day.thursday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="friday" name="byday[]" value="FR" {{ in_array('FR', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="friday">@lang('day.friday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="saturday" name="byday[]" value="SA" {{ in_array('SA', old('byday', $byday)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="saturday">@lang('day.saturday')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="sunday" name="byday[]" value="SU" {{ in_array('SU', old('byday', $byday)) ? 'checked' : '' }}>
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
                                    <option value="never" {{ old('end_condition', $end_condition) == 'never' ? 'selected' : '' }}>@lang('never')</option>
                                    <option value="on_date" {{ old('end_condition', $end_condition) == 'on_date' ? 'selected' : '' }}>@lang('on_date')</option>
                                    <option value="after_occurrences" {{ old('end_condition', $end_condition) == 'after_occurrences' ? 'selected' : '' }}>@lang('after_occurrences')</option>
                                </select>
                                @error('end_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="end-date" style="display:none;">
                                <label for="until" class="form-label">@lang('until'):</label>
                                <input type="date" class="form-control @error('until') is-invalid @enderror" id="until" name="until" value="{{ old('until', $until) }}">
                                @error('until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 recurrence-fields" id="end-occurrences" style="display:none;">
                                <label for="count" class="form-label">@lang('count'):</label>
                                <input type="number" class="form-control @error('count') is-invalid @enderror" id="count" name="count" value="{{ old('count', $count) ?? '1' }}" min="1">
                                @error('count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-3">
            <label>@lang('recurrence_summary'):</label>
            <input type="text" class="form-control" value="{{ $recurrenceSummary }}" readonly>
        </div>

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
