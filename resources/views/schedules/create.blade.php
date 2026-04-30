@extends('layouts.app')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <h1>@lang('schedules.create')</h1>

    <form action="{{ route('schedules.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="activity_id" class="form-label">@lang('activities.index'):</label>
            <select class="form-select @error('activity_id') is-invalid @enderror" id="activity_id" name="activity_id" required></select>
            @error('activity_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="start_date" class="form-label">@lang('start_date'):</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" required min="{{ now()->format('Y-m-d') }}">
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label for="end_date" class="form-label">@lang('end_date'):</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" required min="{{ now()->format('Y-m-d') }}">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    
        <div class="mb-3">
            <div class="table-responsive">
                <table class="table table-borderless table-sm align-middle">
                    <thead>
                        <tr>
                            <th>@lang('service_types.index')</th>
                            <th class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="select_all_include" title="{{ __('select_all') }}">
                                    <label class="form-check-label mb-0" for="select_all_include">@lang('include')?</label>
                                </div>
                            </th>
                            <th style="width: 100px;">@lang('count')</th>
                            <th style="width: 150px;" class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="select_all_repeatable" title="{{ __('select_all') }}">
                                    <label class="form-check-label mb-0" for="select_all_repeatable">@lang('repeatable')?</label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceTypes as $serviceType)
                            <tr class="service-type-row" data-activity-ids="{{ $serviceType->activities->pluck('id')->implode(',') }}">
                                <td>
                                    {{ $serviceType->name }}
                                    <small class="text-muted">({{ $serviceType->activities->pluck('name')->implode(', ') }})</small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-check-inline d-flex justify-content-center">
                                        <input class="form-check-input @error("service_types.{$serviceType->id}.include") is-invalid @enderror" type="checkbox"
                                            name="service_types[{{ $serviceType->id }}][include]" value="1"
                                            id="include_{{ $serviceType->id }}"
                                            {{ old("service_types.{$serviceType->id}.include") ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <input type="number"
                                        name="service_types[{{ $serviceType->id }}][count]"
                                        class="form-control @error("service_types.{$serviceType->id}.count") is-invalid @enderror"
                                        min="1"
                                        value="{{ old("service_types.{$serviceType->id}.count", 1) }}">
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-check-inline d-flex justify-content-center">
                                        <input class="form-check-input @error("service_types.{$serviceType->id}.is_repeatable") is-invalid @enderror" type="checkbox"
                                            name="service_types[{{ $serviceType->id }}][is_repeatable]" value="1"
                                            id="is_repeatable_{{ $serviceType->id }}"
                                            {{ old("service_types.{$serviceType->id}.is_repeatable") ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="2">
                                @error('service_types.*.include')
                                    <div class="small text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                @error('service_types.*.count')
                                    <div class="small text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                @error('service_types.*.is_repeatable')
                                    <div class="small text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">@lang('generate')</button>

                <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
            </div>
        </div>
    </form>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#activity_id').select2({
            placeholder: "{{ __('choose') }}...",
            allowClear: true,
            ajax: {
                url: "{{ route('ajax.activities') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, $params) {
                    allowInitialLoad = false;

                    return {
                        results: data.items,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
        });

        @if(old('activity_id'))
            @php $oldActivity = App\Models\Activity::find(old('activity_id'), ['id', 'name']); @endphp
            @if($oldActivity)
                $('#activity_id').append(new Option("{{ $oldActivity->name }}", "{{ $oldActivity->id }}", true, true)).trigger('change');
            @endif
        @endif

        function updateSelectAll(columnCheckboxes, selectAllEl) {
            const visible = columnCheckboxes.filter(':visible');
            const checkedCount = visible.filter(':checked').length;
            if (checkedCount === 0) {
                selectAllEl.prop({ checked: false, indeterminate: false });
            } else if (checkedCount === visible.length) {
                selectAllEl.prop({ checked: true, indeterminate: false });
            } else {
                selectAllEl.prop({ checked: false, indeterminate: true });
            }
        }

        const $includeCheckboxes = $('input[name*="[include]"]');
        const $repeatableCheckboxes = $('input[name*="[is_repeatable]"]');
        const $selectAllInclude = $('#select_all_include');
        const $selectAllRepeatable = $('#select_all_repeatable');

        $selectAllInclude.on('change', function() {
            $includeCheckboxes.filter(function() {
                return $(this).closest('tr').is(':visible');
            }).prop('checked', $(this).prop('checked'));
        });

        $selectAllRepeatable.on('change', function() {
            $repeatableCheckboxes.filter(function() {
                return $(this).closest('tr').is(':visible');
            }).prop('checked', $(this).prop('checked'));
        });

        $includeCheckboxes.on('change', function() {
            updateSelectAll($includeCheckboxes, $selectAllInclude);
        });

        $repeatableCheckboxes.on('change', function() {
            updateSelectAll($repeatableCheckboxes, $selectAllRepeatable);
        });

        $('#activity_id').on('change', function() {
            const selectedActivityId = $(this).val();
            $('.service-type-row').each(function() {
                const activityIds = String($(this).data('activity-ids') || '').split(',');
                if (!selectedActivityId || activityIds.includes(selectedActivityId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).find('input[type="checkbox"]').prop('checked', false);
                    $(this).find('input[type="number"]').val(1);
                }
            });
            updateSelectAll($includeCheckboxes, $selectAllInclude);
            updateSelectAll($repeatableCheckboxes, $selectAllRepeatable);
        });
    </script>
@endsection