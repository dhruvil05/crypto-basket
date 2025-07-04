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

        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const isSuccess = modal.querySelector('.success-modal-body');
            if (isSuccess) {
                // ✅ OK Button
                const okBtn = modal.querySelector('.modal-footer .btn-primary');
                if (okBtn && !okBtn.dataset.redirectBound) {
                    okBtn.dataset.redirectBound = "true";
                    okBtn.addEventListener('click', redirectToWallet);
                }

                // ✅ Close Icon
                const closeIcon = modal.querySelector('.modal-header button[data-bs-dismiss="modal"]');
                if (closeIcon && !closeIcon.dataset.redirectBound) {
                    closeIcon.dataset.redirectBound = "true";
                    closeIcon.addEventListener('click', () => {
                        closeIcon.blur();
                        setTimeout(redirectToWallet, 50);
                    });
                }
            }
        });
    });
</script>
@endpush


{{-- @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const redirectToWallet = () => {
            window.location.href = "{{ route('platform.wallet') }}";
        };

        const observer = new MutationObserver(() => {
            const modal = document.querySelector('.modal.show .success-modal-body');
            if (modal) {
                const closeIcon = modal.closest('.modal')?.querySelector('[data-bs-dismiss="modal"]');
                const okBtn = modal.closest('.modal')?.querySelector('.modal-footer .btn'); // generic btn

                if (closeIcon) closeIcon.onclick = redirectToWallet;
                if (okBtn) okBtn.onclick = redirectToWallet;

                observer.disconnect();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>
@endpush --}}


{{-- @push('scripts')
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
@endpush --}}
