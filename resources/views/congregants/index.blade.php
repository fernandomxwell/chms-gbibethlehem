@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a class="btn btn-primary" href="{{ route('congregants.create') }}">@lang('congregants.create')</a>
        <a class="btn btn-success" href="{{ route('congregants.export') }}">@lang('congregants.export')</a>
        <a class="btn btn-secondary" href="{{ route('congregants.import.form') }}">@lang('congregants.import')</a>
        <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
            form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
        </button>
    </div>

    <form action="{{ route('congregants.index') }}" method="GET">
        <div class="row my-1">
            <div class="col-md-3 my-1">
                <select class="form-select @error('status') is-invalid @enderror" name="status" onchange="this.form.submit()">
                    <option value="">@lang('all_statuses')</option>
                    <option value="member" {{ old('status') == 'member' ? 'selected' : (request('status') == 'member' ? 'selected' : '') }}>@lang('member')</option>
                    <option value="sympathizer" {{ old('status') == 'sympathizer' ? 'selected' : (request('status') == 'sympathizer' ? 'selected' : '') }}>@lang('sympathizer')</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3 my-1">
                <select class="form-select @error('gender') is-invalid @enderror" name="gender" onchange="this.form.submit()">
                    <option value="">@lang('all_genders')</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : (request('gender') == 'male' ? 'selected' : '') }}>@lang('male')</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : (request('gender') == 'female' ? 'selected' : '') }}>@lang('female')</option>
                </select>
                @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 my-1">
                <div class="input-group">
                    <input type="text" class="form-control @error('search') is-invalid @enderror" name="search" value="{{ old('search', request('search')) }}" maxlength="100" placeholder="@lang('search_by_name_email_phone')">
                    <button class="btn btn-outline-secondary" type="submit">@lang('search')</button>
                    @error('search')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <form id="bulk-form" action="{{ route('congregants.bulk-destroy') }}" method="POST">
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
                        <th class="text-nowrap">@lang('gender')</th>
                        <th class="text-nowrap">@lang('phone_number')</th>
                        <th class="text-nowrap">@lang('email')</th>
                        <th class="text-nowrap">@lang('status')</th>
                        <th class="text-nowrap">@lang('actions')</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($congregants as $index => $congregant)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $congregant->id }}" class="bulk-checkbox"></td>
                        <td>{{ paginatedIndex($index + 1, $congregants->currentPage(), $congregants->perPage()) }}</td>
                        <td class="text-nowrap">{!! highlightMatch($congregant->full_name, request('search')) !!}</td>
                        <td>@lang($congregant->gender)</td>
                        <td>{!! highlightMatch($congregant->phone_number, request('search')) !!}</td>
                        <td>{!! highlightMatch($congregant->email, request('search')) !!}</td>
                        <td>@lang($congregant->status)</td>
                        <td class="text-nowrap">
                            <a class="btn btn-info text-light mr-1 mb-1" href="{{ route('congregants.show',$congregant->id) }}">@lang('show')</a>
                            <a class="btn btn-success mr-1 mb-1" href="{{ route('congregants.edit',$congregant->id) }}">@lang('edit')</a>
                            <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $congregant->id }}">@lang('delete')</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">@lang('no_records_found')</td>
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
                        @lang('congregants.are_you_sure')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('cancel')</button>
                        <form action="{{ route('congregants.destroy', $congregant->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">@lang('delete')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('congregants.are_you_sure_bulk')])
@endsection
