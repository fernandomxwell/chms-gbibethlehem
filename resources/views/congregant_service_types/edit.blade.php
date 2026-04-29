@extends('layouts.app')

@section('content')
    <h1>@lang('congregant_services.edit')</h1>

    <form action="{{ route('congregant_services.update', $congregant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="congregant_id" class="form-label">@lang('congregants.index'):</label>
            <select class="form-select @error('congregant_id') is-invalid @enderror" id="congregant_id" name="congregant_id" disabled></select>
            @error('congregant_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="accordion mb-3">
            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                    <button class="accordion-button bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        @lang('activities.index')
                    </button>
                </h2>
                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                    <div class="accordion-body">
                        <div class="row">
                            @foreach($activities as $activity)
                                <div class="col-md-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('activity_ids') is-invalid @enderror" type="checkbox" name="activity_ids[]" value="{{ $activity->id }}" {{ in_array($activity->id, old('activity_ids', $congregant->activities->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $activity->name }}">{{ $activity->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                            @error('activity_ids')
                                <div class="small text-danger">{{ $message }}</div>
                            @enderror
                            @error('activity_ids.*')
                                <div class="small text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                    <button class="accordion-button bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                        @lang('service_types.index')
                    </button>
                </h2>
                <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label for="activity_filter" class="form-label">@lang('filter_by_activity'):</label>
                            <select class="form-select" id="activity_filter" onchange="filterServiceTypes()">
                                <option value="">@lang('all_activities')</option>
                                @foreach($activities as $activity)
                                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $allActivities = $serviceTypes
                                ->flatMap(fn($st) => $st->activities)
                                ->unique('id')
                                ->sortBy('name');

                            $selectedServiceTypes = $congregant->serviceTypesPivot
                                ->filter(fn($p) => $p->activity_id)
                                ->groupBy('activity_id')
                                ->map(fn($pivots) => $pivots->pluck('service_type_id')->toArray());
                        @endphp

                        @foreach($allActivities as $activity)
                            <div class="service-type-group mb-3" data-activity-id="{{ $activity->id }}">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input class="form-check-input select-all-st" type="checkbox" id="select_all_st_{{ $activity->id }}" data-activity-id="{{ $activity->id }}">
                                    <h6 class="text-muted mb-0">{{ $activity->name }}</h6>
                                </div>
                                <div class="row">
                                    @foreach($serviceTypes->filter(fn($st) => $st->activities->contains('id', $activity->id)) as $serviceType)
                                        <div class="col-md-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input @error('service_types.' . $activity->id) is-invalid @enderror" type="checkbox" name="service_types[{{ $activity->id }}][]" value="{{ $serviceType->id }}"
                                                    @php
                                                        $isChecked = false;
                                                        if (old('service_types.' . $activity->id)) {
                                                            $isChecked = in_array($serviceType->id, old('service_types.' . $activity->id));
                                                        } elseif (isset($selectedServiceTypes[$activity->id])) {
                                                            $isChecked = in_array($serviceType->id, $selectedServiceTypes[$activity->id]);
                                                        }
                                                        echo $isChecked ? 'checked' : '';
                                                    @endphp
                                                >
                                                <label class="form-check-label" for="{{ $serviceType->name }}">{{ $serviceType->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @error('service_types')
                            <div class="small text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="hidden" name="can_serve_consecutively" value="0">
                
                <input class="form-check-input" type="checkbox" name="can_serve_consecutively" value="1" id="can_serve_consecutively" 
                    @if(old('can_serve_consecutively', $congregant->can_serve_consecutively)) checked @endif
                >
                <label class="form-check-label" for="can_serve_consecutively">
                    @lang('willing_to_serve')
                </label>
            </div>
            @error('can_serve_consecutively')
                <div class="small text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">@lang('submit')</button>

        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection

@section('javascript')
    <script>
        let defaultOption = {
            id: {{ $congregant->id }},
            text: " {{ $congregant->full_name }}"
        };
        let newOption = new Option(defaultOption.text, defaultOption.id, true, true);
        $('#congregant_id').append(newOption).trigger('change');

        function filterServiceTypes() {
            const selectedActivityId = document.getElementById('activity_filter').value;
            document.querySelectorAll('.service-type-group').forEach(group => {
                const activityId = group.dataset.activityId;
                group.style.display = (!selectedActivityId || activityId === selectedActivityId) ? 'block' : 'none';
            });
        }

        function updateSelectAllSt(activityId) {
            const $boxes = $('input[name="service_types[' + activityId + '][]"]');
            const $selectAll = $('#select_all_st_' + activityId);
            const checked = $boxes.filter(':checked').length;
            if (checked === 0) {
                $selectAll.prop({ checked: false, indeterminate: false });
            } else if (checked === $boxes.length) {
                $selectAll.prop({ checked: true, indeterminate: false });
            } else {
                $selectAll.prop({ checked: false, indeterminate: true });
            }
        }

        $('.select-all-st').on('change', function() {
            const activityId = $(this).data('activity-id');
            $('input[name="service_types[' + activityId + '][]"]').prop('checked', $(this).prop('checked'));
        });

        $('[name^="service_types["]').on('change', function() {
            const match = $(this).attr('name').match(/service_types\[(\d+)\]/);
            if (match) updateSelectAllSt(match[1]);
        });

        // Initialize select all states based on existing checked values
        $('.select-all-st').each(function() {
            updateSelectAllSt($(this).data('activity-id'));
        });
    </script>
@endsection