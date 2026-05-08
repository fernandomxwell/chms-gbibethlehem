@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.index')</h1>

    @include('layouts.error')
    @include('layouts.success')

    <div class="d-flex flex-wrap gap-2 mb-2">
        @can('congregants.create')
            <a class="btn btn-primary" href="{{ route('congregants.create') }}">@lang('congregants.create')</a>
        @endif
        @can('congregants.view')
            <a class="btn btn-warning" href="{{ route('congregants.export') }}">@lang('congregants.export')</a>
        @endif
        @can('congregants.create')
            <a class="btn btn-secondary" href="{{ route('congregants.import.form') }}">@lang('congregants.import')</a>
        @endif
        @can('congregants.delete')
            <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled
                form="bulk-form" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                @lang('bulk_delete') (<span id="bulk-selected-count">0</span>)
            </button>
        @endif
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

    <x-crud-table :items="$congregants" :bulk-destroy-route="route('congregants.bulk-destroy')">
        <x-slot:headers>
            <th class="text-nowrap">@lang('No')</th>
            <th class="text-nowrap">@lang('full_name')</th>
            <th class="text-nowrap">@lang('gender')</th>
            <th class="text-nowrap">@lang('phone_number')</th>
            <th class="text-nowrap">@lang('email')</th>
            <th class="text-nowrap">@lang('status')</th>
            <th class="text-nowrap">@lang('actions')</th>
        </x-slot:headers>
        <x-slot:body>
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
                        @can('congregants.view')
                            <a class="btn btn-info text-light mr-1 mb-1" href="{{ route('congregants.show', $congregant->id) }}">@lang('show')</a>
                        @endif
                        @can('congregants.edit')
                            <a class="btn btn-success mr-1 mb-1" href="{{ route('congregants.edit', $congregant->id) }}">@lang('edit')</a>
                        @endif
                        @can('congregants.delete')
                            <button type="button" class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $congregant->id }}">@lang('delete')</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">@lang('no_records_found')</td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-crud-table>

    @include('layouts.delete-modals', [
        'items' => $congregants,
        'permission' => 'congregants.delete',
        'destroyRoute' => 'congregants.destroy',
        'confirmText' => __('congregants.are_you_sure'),
    ])

    @include('layouts.bulk-delete', ['bulkDeleteConfirmText' => __('congregants.are_you_sure_bulk')])
@endsection
