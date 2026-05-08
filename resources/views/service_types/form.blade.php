<div class="mb-3">
    <label for="name" class="form-label">@lang('name'):</label>
    <input type="text" name="name" id="name" value="{{ old('name', $serviceType?->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">@lang('description'):</label>
    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description', $serviceType?->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">@lang('service_types.select_activities'):</label>
    <div class="vstack gap-2">
        @foreach($activities as $activity)
            <div class="form-check">
                <input type="checkbox" name="activities[]" id="activity_{{ $activity->id }}" value="{{ $activity->id }}" class="form-check-input"
                    {{ in_array($activity->id, old('activities', $serviceType?->activities->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                <label for="activity_{{ $activity->id }}" class="form-check-label">{{ $activity->name }}</label>
            </div>
        @endforeach
    </div>
</div>
