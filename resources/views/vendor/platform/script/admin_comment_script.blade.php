<script>
    // document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            const statusSelect = document.getElementById('status-select');
            const rejectionComment = document.getElementById('admin-comment')?.closest('.form-group');

            function toggleRejectionComment() {
                if (statusSelect && rejectionComment) {
                    rejectionComment.style.display = statusSelect.value === 'rejected' ? 'block' : 'none';
                }
            }

            if (statusSelect && rejectionComment) {
                toggleRejectionComment(); // Show/hide on load
                statusSelect.addEventListener('change', toggleRejectionComment); // Show/hide on change
            }
        }, 150); // Small delay for Orchid render
    // });
</script>