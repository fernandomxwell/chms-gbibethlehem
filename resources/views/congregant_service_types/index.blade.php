@extends('layouts.app')

@section('content')
    <h1>@lang('congregant_services.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a class="btn btn-primary" href="{{ route('congregant_services.create') }}">@lang('congregant_services.create')</a>
        <a class="btn btn-success" href="{{ route('congregant_services.export') }}">@lang('congregant_services.export')</a>
        <a class="btn btn-secondary" href="{{ route('congregant_services.import.form') }}">@lang('congregant_services.import')</a>
        <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
            form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
        </button>
    </div>

    <form action="{{ route('congregant_services.index') }}" method="GET">
        <div class="row my-1">
            <div class="col-md-6 my-1 offset-md-6">
                <div class="input-group">
                    <input type="text" class="form-control @error('search') is-invalid @enderror" name="search" value="{{ old('search', request('search')) }}" maxlength="100" placeholder="@lang('search_by_name_activity_service_type')" aria-label="@lang('search_by_name_service_type')">
                    <button class="btn btn-outline-secondary" type="submit">@lang('search')</button>
                    @error('search')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <form id="bulk-form" action="{{ route('congregant_services.bulk-destroy') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="align-middle table-light">
                    <tr>
                        <th class="text-nowrap" style="width:40px">
                            <input type="checkbox" id="bulk-select-all" title="@lang('select_all')">
                        </th>
                        <th class="text-nowrap">@lang('No')</th>
                        <th class="text-nowrap">@lang('full_name')</th>
                        <th class="text-nowrap" style="min-width: 200px">@lang('activities.index')</th>
                        <th class="text-nowrap" style="min-width: 200px">@lang('notes')</th>
                        <th class="text-nowrap">@lang('actions')</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($congregants as $index => $congregant)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $congregant->id }}" class="bulk-checkbox"></td>
                        <td>{{ paginatedIndex($index + 1, $congregants->currentPage(), $congregants->perPage()) }}</td>
                        <td class="text-nowrap">{!! highlightMatch($congregant->full_name, request('search')) !!}</td>
                        <td>
                            @php
                                $activitiesWithServiceTypes = $congregant->serviceTypesPivot
                                    ->filter(fn($p) => $p->activity_id)
                                    ->groupBy('activity_id');
                            @endphp
                            @if($activitiesWithServiceTypes->isNotEmpty())
                                <ul class="mb-1 ps-3">
                                    @foreach($activitiesWithServiceTypes as $activityId => $pivots)
                                        @php
                                            $activityName = $pivots->first()->activity?->name;
                                            $serviceTypes = $pivots->pluck('serviceType.name')->filter();
                                        @endphp
                                        @if($activityName && $serviceTypes->isNotEmpty())
                                            <li><strong>{!! highlightMatch($activityName, request('search')) !!}:</strong>
                                                <ul class="ps-3 mb-0">
                                                    @foreach($serviceTypes as $serviceType)
                                                        <li>{!! highlightMatch($serviceType, request('search')) !!}</li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $congregant->can_serve_consecutively ? __('willing_to_serve') : '' }}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-success mr-1 mb-1" href="{{ route('congregant_services.edit', $congregant->id) }}">@lang('edit')</a>
                            <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $congregant->id }}">@lang('delete')</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">@lang('no_records_found')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $congregants->links() !!}
        </div>
    </form>

    {{-- Individual delete modals --}}
    @foreach ($congregants as $congregant)
        <div class="modal fade" id="deleteModal{{ $congregant->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $congregant->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $congregant->id }}">@lang('confirm_delete')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @lang('congregant_services.are_you_sure')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('cancel')</button>
                        <form action="{{ route('congregant_services.destroy', $congregant->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">@lang('delete')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('congregant_services.are_you_sure_bulk')])
@endsection
