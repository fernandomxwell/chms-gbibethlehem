@props([
    'items',
    'bulkDestroyRoute',
    'sortable' => false,
])

<form id="bulk-form" action="{{ $bulkDestroyRoute }}" method="POST">
    @csrf
    @method('DELETE')

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="align-middle table-light">
                <tr>
                    <th class="text-nowrap" style="width:40px">
                        <input type="checkbox" id="bulk-select-all" title="@lang('select_all')">
                    </th>
                    {{ $headers }}
                </tr>
            </thead>
            <tbody @if($sortable) id="sortable-tbody" @endif>
                {{ $body }}
            </tbody>
        </table>
        {!! $items->links() !!}
    </div>
</form>
