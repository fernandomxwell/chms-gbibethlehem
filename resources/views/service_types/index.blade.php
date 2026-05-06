@extends('layouts.app')

@section('content')
    <h1>@lang('service_types.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a class="btn btn-primary" href="{{ route('service_types.create') }}">@lang('service_types.create')</a>
        <a class="btn btn-success" href="{{ route('service_types.export') }}">@lang('service_types.export')</a>
        <a class="btn btn-secondary" href="{{ route('service_types.import.form') }}">@lang('service_types.import')</a>
        <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
            form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
        </button>
    </div>

    @if(request('search'))
        <div class="alert alert-info py-2 mb-2">
            <small><i class="bi bi-info-circle"></i> @lang('service_types.reorder_disabled_search')</small>
        </div>
    @endif

    <form action="{{ route('service_types.index') }}" method="GET">
        <div class="row my-1">
            <div class="col-md-6 my-1 offset-md-6">
                <div class="input-group">
                    <input type="text" class="form-control @error('search') is-invalid @enderror" name="search" value="{{ old('search', request('search')) }}" maxlength="100" placeholder="@lang('search_by_name_activity')">
                    <button class="btn btn-outline-secondary" type="submit">@lang('search')</button>
                    @error('search')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <div id="reorder-toast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body" id="reorder-toast-msg">@lang('service_types.success_reorder')</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <form id="bulk-form" action="{{ route('service_types.bulk-destroy') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="align-middle table-light">
                    <tr>
                        <th class="text-nowrap" style="width:40px">
                            <input type="checkbox" id="bulk-select-all" title="@lang('select_all')">
                        </th>
                        @if(!request('search'))
                            <th class="text-nowrap text-center" style="width:40px" title="@lang('service_types.drag_to_reorder')">
                                <i class="bi bi-grip-vertical text-muted"></i>
                            </th>
                        @endif
                        <th class="text-nowrap">@lang('No')</th>
                        <th class="text-nowrap" style="min-width: 150px">@lang('name')</th>
                        <th class="text-nowrap">@lang('activities.index')</th>
                        <th class="text-nowrap">@lang('actions')</th>
                    </tr>
                </thead>
                <tbody id="sortable-tbody">
                    @forelse ($serviceTypes as $index => $serviceType)
                        <tr data-id="{{ $serviceType->id }}">
                            <td><input type="checkbox" name="ids[]" value="{{ $serviceType->id }}" class="bulk-checkbox"></td>
                            @if(!request('search'))
                                <td class="text-center drag-handle" style="cursor: grab" title="@lang('service_types.drag_to_reorder')">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </td>
                            @endif
                            <td>{{ paginatedIndex($index + 1, $serviceTypes->currentPage(), $serviceTypes->perPage()) }}</td>
                            <td>{!! highlightMatch($serviceType->name, request('search')) !!}</td>
                            <td>
                                @forelse($serviceType->activities as $activity)
                                    <span class="badge bg-info me-1">{!! highlightMatch($activity->name, request('search')) !!}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="text-nowrap">
                                <a class="btn btn-info text-light mr-1 mb-1" href="{{ route('service_types.show', $serviceType->id) }}">@lang('show')</a>
                                <a class="btn btn-success mr-1 mb-1" href="{{ route('service_types.edit', $serviceType->id) }}">@lang('edit')</a>
                                <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $serviceType->id }}">@lang('delete')</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ request('search') ? 5 : 6 }}" class="text-center">@lang('no_records_found')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {!! $serviceTypes->links() !!}
        </div>
    </form>

    {{-- Individual delete modals --}}
    @foreach ($serviceTypes as $serviceType)
        <div class="modal fade" id="deleteModal{{ $serviceType->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $serviceType->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $serviceType->id }}">@lang('confirm_delete')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @lang('service_types.are_you_sure')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('cancel')</button>
                        <form action="{{ route('service_types.destroy', $serviceType->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">@lang('delete')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('service_types.are_you_sure_bulk')])

@endsection

@if(!request('search'))
@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        (function () {
            const tbody = document.getElementById('sortable-tbody');
            if (!tbody) return;

            const reorderUrl = '{{ route('service_types.reorder') }}';
            const csrfToken = '{{ csrf_token() }}';
            const toastEl = document.getElementById('reorder-toast');
            const toastMsg = document.getElementById('reorder-toast-msg');
            const errorMsg = '@lang('service_types.reorder_error')';
            const successMsg = '@lang('service_types.success_reorder')';

            const bsToast = new bootstrap.Toast(toastEl, { delay: 2500 });

            Sortable.create(tbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'table-warning',
                onEnd: function () {
                    const ids = Array.from(tbody.querySelectorAll('tr[data-id]'))
                        .map(tr => tr.dataset.id);

                    fetch(reorderUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ids }),
                    })
                    .then(res => {
                        if (!res.ok) throw new Error();
                        toastEl.querySelector('.toast').classList.replace('text-bg-danger', 'text-bg-success');
                        toastMsg.textContent = successMsg;
                    })
                    .catch(() => {
                        toastEl.querySelector('.toast').classList.replace('text-bg-success', 'text-bg-danger');
                        toastMsg.textContent = errorMsg;
                    })
                    .finally(() => bsToast.show());
                },
            });
        })();
    </script>
@endsection
@endif
