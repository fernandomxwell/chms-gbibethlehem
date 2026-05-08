@extends('layouts.app')

@section('content')
    <h1>@lang('activities.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        @can('activities.create')
            <a class="btn btn-primary" href="{{ route('activities.create') }}">@lang('activities.create')</a>
        @endif
        @can('activities.delete')
            <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
                form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
            </button>
        @endif
    </div>

    @if(request('search'))
        <div class="alert alert-info py-2 mb-2">
            <small><i class="bi bi-info-circle"></i> @lang('activities.reorder_disabled_search')</small>
        </div>
    @endif

    <form action="{{ route('activities.index') }}" method="GET">
        <div class="row my-1">
            <div class="col-md-6 my-1 offset-md-6">
                <div class="input-group">
                    <input type="text" class="form-control @error('search') is-invalid @enderror" name="search" value="{{ old('search', request('search')) }}" maxlength="100" placeholder="@lang('search_by_name')">
                    <button class="btn btn-outline-secondary" type="submit">@lang('search')</button>
                    @error('search')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    @if(!request('search'))
        <div id="reorder-toast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive">
                <div class="d-flex">
                    <div class="toast-body" id="reorder-toast-msg">@lang('activities.success_reorder')</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    @endif

    <x-crud-table :items="$activities" :bulk-destroy-route="route('activities.bulk-destroy')" :sortable="!request('search')">
        <x-slot:headers>
            @if(!request('search'))
                <th class="text-nowrap text-center" style="width:40px" title="@lang('activities.drag_to_reorder')">
                    <i class="bi bi-grip-vertical text-muted"></i>
                </th>
            @endif
            <th class="text-nowrap">@lang('No')</th>
            <th class="text-nowrap">@lang('name')</th>
            <th class="text-nowrap">@lang('start_time')</th>
            <th class="text-nowrap">@lang('recurrence_summary')</th>
            <th class="text-nowrap">@lang('actions')</th>
        </x-slot:headers>
        <x-slot:body>
            @forelse ($activities as $index => $activity)
                <tr data-id="{{ $activity->id }}">
                    <td><input type="checkbox" name="ids[]" value="{{ $activity->id }}" class="bulk-checkbox"></td>
                    @if(!request('search'))
                        <td class="text-center drag-handle" style="cursor: grab" title="@lang('activities.drag_to_reorder')">
                            <i class="bi bi-grip-vertical text-muted"></i>
                        </td>
                    @endif
                    <td>{{ paginatedIndex($index + 1, $activities->currentPage(), $activities->perPage()) }}</td>
                    <td>{!! highlightMatch($activity->name, request('search')) !!}</td>
                    <td class="text-nowrap">{{ $activity->start_time }}</td>
                    <td>{{ $recurrenceSummaries[$activity->id] }}</td>
                    <td class="text-nowrap">
                        @can('activities.view')
                            <a class="btn btn-info text-light mr-1 mb-1" href="{{ route('activities.show', $activity->id) }}">@lang('show')</a>
                        @endif
                        @can('activities.edit')
                            <a class="btn btn-success mr-1 mb-1" href="{{ route('activities.edit', $activity->id) }}">@lang('edit')</a>
                        @endif
                        @can('activities.delete')
                            <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $activity->id }}">@lang('delete')</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ request('search') ? 6 : 7 }}" class="text-center">@lang('no_records_found')</td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-crud-table>

    @include('layouts.delete-modals', [
        'items' => $activities,
        'permission' => 'activities.delete',
        'destroyRoute' => 'activities.destroy',
        'confirmText' => __('activities.are_you_sure'),
    ])

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('activities.are_you_sure_bulk')])
@endsection

@if(!request('search'))
@section('javascript')
    @include('layouts.reorder-script', [
        'reorderRoute' => route('activities.reorder'),
        'successMsg' => __('activities.success_reorder'),
        'errorMsg' => __('activities.reorder_error'),
    ])
@endsection
@endif
