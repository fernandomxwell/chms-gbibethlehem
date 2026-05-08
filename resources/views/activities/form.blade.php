<div class="mb-3">
    <label for="name" class="form-label">@lang('name'):</label>
    <input type="text" name="name" id="name" value="{{ old('name', $activity?->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">@lang('description'):</label>
    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $activity?->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="start_time" class="form-label">@lang('start_time') <small class="text-muted">(@lang('fill_in_the_next_nearest_schedule'))</small>:</label>
    <input type="datetime-local" name="start_time" id="start_time"
        value="{{ old('start_time', isset($activity) ? $activity->start_time->format('Y-m-d H:i') : '') }}"
        class="form-control @error('start_time') is-invalid @enderror"
        @if(!isset($activity)) min="{{ now()->format('Y-m-d H:i') }}" @endif
        required>
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
                            <option value="NONE" {{ old('frequency', $frequency ?? '') == 'NONE' ? 'selected' : '' }}>@lang('none')</option>
                            <option value="DAILY" {{ old('frequency', $frequency ?? '') == 'DAILY' ? 'selected' : '' }}>@lang('daily')</option>
                            <option value="WEEKLY" {{ old('frequency', $frequency ?? '') == 'WEEKLY' ? 'selected' : '' }}>@lang('weekly')</option>
                            <option value="MONTHLY" {{ old('frequency', $frequency ?? '') == 'MONTHLY' ? 'selected' : '' }}>@lang('monthly')</option>
                            <option value="YEARLY" {{ old('frequency', $frequency ?? '') == 'YEARLY' ? 'selected' : '' }}>@lang('yearly')</option>
                        </select>
                        @error('frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 recurrence-fields" id="interval-group">
                        <label for="interval" class="form-label">@lang('interval'):</label>
                        <input type="number" class="form-control @error('interval') is-invalid @enderror" id="interval" name="interval" value="{{ old('interval', $interval ?? null) ?? '1' }}" min="1">
                        @error('interval')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 recurrence-fields" id="weekly-days" style="display:none;">
                        <span>@lang('days'):</span><br>
                        @foreach(['MO' => 'monday', 'TU' => 'tuesday', 'WE' => 'wednesday', 'TH' => 'thursday', 'FR' => 'friday', 'SA' => 'saturday', 'SU' => 'sunday'] as $value => $key)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('byday') is-invalid @enderror" type="checkbox" id="{{ $key }}" name="byday[]" value="{{ $value }}" {{ in_array($value, old('byday', $byday ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $key }}">@lang('day.' . $key)</label>
                            </div>
                        @endforeach
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
                            <option value="never" {{ old('end_condition', $end_condition ?? '') == 'never' ? 'selected' : '' }}>@lang('never')</option>
                            <option value="on_date" {{ old('end_condition', $end_condition ?? '') == 'on_date' ? 'selected' : '' }}>@lang('on_date')</option>
                            <option value="after_occurrences" {{ old('end_condition', $end_condition ?? '') == 'after_occurrences' ? 'selected' : '' }}>@lang('after_occurrences')</option>
                        </select>
                        @error('end_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 recurrence-fields" id="end-date" style="display:none;">
                        <label for="until" class="form-label">@lang('until'):</label>
                        <input type="date" class="form-control @error('until') is-invalid @enderror" id="until" name="until" value="{{ old('until', $until ?? '') }}" @if(!isset($activity)) min="{{ now()->format('Y-m-d') }}" @endif>
                        @error('until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 recurrence-fields" id="end-occurrences" style="display:none;">
                        <label for="count" class="form-label">@lang('count'):</label>
                        <input type="number" class="form-control @error('count') is-invalid @enderror" id="count" name="count" value="{{ old('count', $count ?? null) ?? '1' }}" min="1">
                        @error('count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@isset($recurrenceSummary)
    <div class="my-3">
        <label>@lang('recurrence_summary'):</label>
        <input type="text" class="form-control" value="{{ $recurrenceSummary }}" readonly>
    </div>
@endisset
