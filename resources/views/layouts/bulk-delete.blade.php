{{-- Params: $bulkDeleteConfirmText (string) --}}

<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('confirm_delete')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $bulkDeleteConfirmText }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('cancel')</button>
                <button type="submit" form="bulk-form" class="btn btn-danger">@lang('delete')</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('bulk-select-all');
    const getCheckboxes = () => document.querySelectorAll('.bulk-checkbox');
    const deleteBtn = document.getElementById('bulk-delete-btn');
    const countSpan = document.getElementById('bulk-selected-count');

    function updateState() {
        const all = getCheckboxes();
        const checked = document.querySelectorAll('.bulk-checkbox:checked').length;
        countSpan.textContent = checked;
        deleteBtn.disabled = checked === 0;
        if (selectAll) {
            selectAll.indeterminate = checked > 0 && checked < all.length;
            selectAll.checked = all.length > 0 && checked === all.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            getCheckboxes().forEach(cb => cb.checked = this.checked);
            updateState();
        });
    }

    getCheckboxes().forEach(cb => cb.addEventListener('change', updateState));
    updateState();
});
</script>
