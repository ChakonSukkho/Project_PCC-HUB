// Empty student data - no rankings yet
        const studentsData = [];
        let currentFilter = 'all';

        function renderRankings(students = studentsData) {
            const rankingsBody = document.getElementById('rankings-body');
            rankingsBody.innerHTML = '';
            
            if (students.length === 0) {
                // Show empty state when no students
                const emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.innerHTML = `
                    <div class="empty-state-icon">📊</div>
                    <h3>No Rankings Available</h3>
                    <p>Student rankings will appear here once merit points are recorded.</p>
                `;
                rankingsBody.appendChild(emptyState);
                return;
            }
        }

        function filterRankings(type) {
            currentFilter = type;
            
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Always show empty state since no data
            renderRankings([]);
        }

        function refreshRankings() {
            const rankingsBody = document.getElementById('rankings-body');
            rankingsBody.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
            
            setTimeout(() => {
                // Show empty state after loading
                renderRankings([]);
            }, 1000);
        }

        // Initialize the interface
        document.addEventListener('DOMContentLoaded', function() {
            renderRankings();
            
            // Add interactive effects to nav links - FIXED VERSION
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                // Only add click prevention for links without href or with href="#"
                if (link.getAttribute('href') === '#' || !link.getAttribute('href')) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Update active state
                        navLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Create ripple effect
                        createRippleEffect(this, e);
                    });
                } else {
                    // For links with actual URLs, just add ripple effect without preventing navigation
                    link.addEventListener('click', function(e) {
                        createRippleEffect(this, e);
                        // Navigation will proceed naturally
                    });
                }
            });

            // Add parallax effect to background decorations
            window.addEventListener('mousemove', function(e) {
                const decorations = document.querySelectorAll('.bg-decoration');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                decorations.forEach((decoration, index) => {
                    const moveX = (x - 0.5) * 20 * (index + 1);
                    const moveY = (y - 0.5) * 20 * (index + 1);
                    decoration.style.transform = `translate(${moveX}px, ${moveY}px)`;
                });
            });

            // Add hover effects to podium places
            document.querySelectorAll('.podium-place').forEach(place => {
                place.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                });
                
                place.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Function to create ripple effect
        function createRippleEffect(element, event) {
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';

            element.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Add CSS keyframes for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);