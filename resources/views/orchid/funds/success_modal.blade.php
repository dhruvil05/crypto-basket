<div class="text-center p-4 success-modal-body">
    <h5 class="text-success"><strong>✅ Funds Under Review!</strong></h5>
    <p>Our team is reviewing your deposit.<br>
        Once approved, your funds will appear on your dashboard.</p>
</div>

@push('scripts')
<script>
    document.addEventListener('turbo:load', () => {
        const redirectToWallet = () => {
            window.location.href = "{{ route('platform.wallet') }}";
        };

        // ✅ Find the active modal containing our success text
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const isSuccess = modal.querySelector('.success-modal-body');
            if (isSuccess) {
                // ❌ Close icon
                const closeIcon = modal.querySelector('.modal-header button[data-bs-dismiss="modal"]');
                if (closeIcon) {
                    closeIcon.addEventListener('click', redirectToWallet);
                }

                // ✅ OK button
                const okBtn = modal.querySelector('.modal-footer .btn-primary');
                if (okBtn) {
                    okBtn.addEventListener('click', redirectToWallet);
                }
            }
        });
    });
</script>
@endpush
