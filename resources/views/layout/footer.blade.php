<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>CryptoVault</h3>
                <a href="#">About Us</a>
                <a href="#">Careers</a>
                <a href="#">Press</a>
                <a href="#">Blog</a>
            </div>
            <div class="footer-section">
                <h3>Products</h3>
                <a href="#">Crypto Baskets</a>
                <a href="#">DeFi Basket</a>
                <a href="#">Gaming Basket</a>
                <a href="#">Layer 1 Basket</a>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <a href="#">Help Center</a>
                <a href="#">Contact Us</a>
                <a href="#">Status</a>
                <a href="#">Security</a>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <a href="#">Terms of Service</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Cookie Policy</a>
                <a href="#">Compliance</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 CryptoVault. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add scroll effect to header
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(0, 0, 0, 0.98)';
        } else {
            header.style.background = 'rgba(0, 0, 0, 0.95)';
        }
    });

    // Animate stats on scroll
    const animateStats = () => {
        const stats = document.querySelectorAll('.stat-item h3');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const value = target.textContent;
                    const numericValue = parseFloat(value.replace(/[^0-9.]/g, ''));

                    if (!isNaN(numericValue)) {
                        let current = 0;
                        const increment = numericValue / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= numericValue) {
                                current = numericValue;
                                clearInterval(timer);
                            }

                            // Determine how to format the displayed number based on the original text content
                            if (value.includes('B+')) { // Check for "B+" explicitly
                                target.textContent = Math.floor(current) + 'B+';
                            } else if (value.includes('M+')) { // Check for "M+" explicitly
                                target.textContent = Math.floor(current) + 'M+';
                            } else if (value.includes('%')) {
                                target.textContent = Math.floor(current) + '%';
                            } else if (value.includes('/')) {
                                // If the original value contains '/', set it to '24/7' directly
                                // This assumes '24/7' is a fixed text and not a counting animation
                                target.textContent = '24/7';
                                clearInterval(
                                    timer); // Stop the animation for '24/7' immediately
                            } else if (value.includes('+')) {
                                target.textContent = Math.floor(current) + '+';
                            } else {
                                // Default case if no specific suffix is found
                                target.textContent = Math.floor(current);
                            }
                        }, 50);
                    }
                    observer.unobserve(target); // Stop observing once animated
                }
            });
        });

        stats.forEach(stat => observer.observe(stat));
    };

    // Initialize animations when the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', animateStats);
</script>
