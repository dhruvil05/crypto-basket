<script>
    function copyReferralCode(event) {
        var input = document.querySelector('input[name="user[referral_code]"]');
        var btn = event ? event.currentTarget : document.getElementById('copy-referral-btn');
        if (input) {
            var code = input.value.trim();
            if (code) {
                // Create a temporary textarea to copy the value
                var temp = document.createElement('textarea');
                temp.value = code;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand("copy");
                document.body.removeChild(temp);
                if (window.platform && typeof window.platform.toast === 'function') {
                    window.platform.toast("Referral code copied!");
                }
                // Toggle button text
                if (btn) {
                    var originalText = btn.innerText;
                    btn.innerText = "Copied";
                    btn.disabled = true;
                    setTimeout(function() {
                        btn.innerText = originalText;
                        btn.disabled = false;
                    }, 1500);
                }
            }
        }
    }

    // Attach event listener to a button with a specific ID or class
    // document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById('copy-referral-btn');
    if (btn) {
        btn.addEventListener('click', copyReferralCode);
    }
    // });
</script>
