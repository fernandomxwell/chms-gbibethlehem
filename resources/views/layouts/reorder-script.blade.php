{{-- Params: $reorderRoute, $successMsg, $errorMsg --}}

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    (function () {
        const tbody = document.getElementById('sortable-tbody');
        if (!tbody) return;

        const reorderUrl = '{{ $reorderRoute }}';
        const csrfToken = '{{ csrf_token() }}';
        const toastEl = document.getElementById('reorder-toast');
        const toastMsg = document.getElementById('reorder-toast-msg');
        const errorMsg = '{{ $errorMsg }}';
        const successMsg = '{{ $successMsg }}';

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
