@extends('layouts.app')

@section('content')
    <h1>@lang('congregant_services.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        @can('congregant_services.create')
            <a class="btn btn-primary" href="{{ route('congregant_services.create') }}">@lang('congregant_services.create')</a>
        @endif
        @can('congregant_services.view')
            <a class="btn btn-warning" href="{{ route('congregant_services.export') }}">@lang('congregant_services.export')</a>
        @endif
        @can('congregant_services.create')
            <a class="btn btn-secondary" href="{{ route('congregant_services.import.form') }}">@lang('congregant_services.import')</a>
        @endif
        @can('congregant_services.delete')
            <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
                form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
            </button>
        @endif
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

    <x-crud-table :items="$congregants" :bulk-destroy-route="route('congregant_services.bulk-destroy')">
        <x-slot:headers>
            <th class="text-nowrap">@lang('No')</th>
            <th class="text-nowrap">@lang('full_name')</th>
            <th class="text-nowrap" style="min-width: 200px">@lang('activities.index')</th>
            <th class="text-nowrap" style="min-width: 200px">@lang('notes')</th>
            <th class="text-nowrap">@lang('actions')</th>
        </x-slot:headers>
        <x-slot:body>
            @forelse ($congregants as $index => $congregant)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $congregant->id }}" class="bulk-checkbox"></td>
                    <td>{{ paginatedIndex($index + 1, $congregants->currentPage(), $congregants->perPage()) }}</td>
                    <td class="text-nowrap">{!! highlightMatch($congregant->full_name, request('search')) !!}</td>
                    <td>
                        @php
                            $activitiesWithServiceTypes = $congregant->serviceTypesPivot
                                ->filter(fn($p) => $p->activity_id)
                                ->sortBy(fn($p) => $p->activity?->sort_order ?? PHP_INT_MAX)
                                ->groupBy('activity_id');
                        @endphp
                        @if($activitiesWithServiceTypes->isNotEmpty())
                            <ul class="mb-1 ps-3">
                                @foreach($activitiesWithServiceTypes as $activityId => $pivots)
                                    @php
                                        $activityName = $pivots->first()->activity?->name;
                                        $serviceTypes = $pivots->sortBy(fn($p) => $p->serviceType?->sort_order ?? PHP_INT_MAX)->pluck('serviceType.name')->filter();
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
                        @can('congregant_services.edit')
                            <a class="btn btn-success mr-1 mb-1" href="{{ route('congregant_services.edit', $congregant->id) }}">@lang('edit')</a>
                        @endif
                        @can('congregant_services.delete')
                            <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $congregant->id }}">@lang('delete')</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">@lang('no_records_found')</td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-crud-table>

    @include('layouts.delete-modals', [
        'items' => $congregants,
        'permission' => 'congregant_services.delete',
        'destroyRoute' => 'congregant_services.destroy',
        'confirmText' => __('congregant_services.are_you_sure'),
    ])

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('congregant_services.are_you_sure_bulk')])
@endsection
