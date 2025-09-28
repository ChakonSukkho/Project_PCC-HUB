<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Statistics - PCC Hub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Animated background elements */
        .bg-decoration {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }

        .bg-decoration:nth-child(1) {
            top: 10%;
            right: -150px;
            animation-delay: 0s;
        }

        .bg-decoration:nth-child(2) {
            bottom: 20%;
            left: -150px;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.1);
            z-index: 10;
            position: relative;
        }

        .logo {
            color: #4f46e5;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo::before {
            content: "🎓";
            font-size: 2rem;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 1rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(79, 70, 229, 0.4);
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .profile-info h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .profile-info p {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .profile-info .student-id {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(79, 70, 229, 0.1), transparent);
            transition: left 0.6s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(79, 70, 229, 0.3);
        }

        .nav-icon {
            font-size: 1.25rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            z-index: 10;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .view-all-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .view-all-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .welcome-section {
            background: linear-gradient(135deg, #1e293b, #334155);
            padding: 3rem;
            border-radius: 1.5rem;
            color: white;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.3), transparent);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.2); opacity: 0.3; }
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .welcome-section p {
            font-size: 1.125rem;
            opacity: 0.8;
            position: relative;
            z-index: 2;
        }

        .trophy-emoji {
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Podium Section */
        .podium-section {
            margin-bottom: 3rem;
        }

        .podium-container {
            display: flex;
            justify-content: center;
            align-items: end;
            margin-bottom: 2rem;
            gap: 2rem;
            perspective: 1000px;
        }

        .podium-place {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: podiumRise 1s ease-out forwards;
            transform: translateY(20px);
            opacity: 0;
        }

        .podium-place:nth-child(1) { animation-delay: 0.2s; }
        .podium-place:nth-child(2) { animation-delay: 0s; }
        .podium-place:nth-child(3) { animation-delay: 0.4s; }

        @keyframes podiumRise {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .empty-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .empty-avatar:hover {
            transform: scale(1.1);
        }

        .first .empty-avatar {
            border-color: #FFD700;
            width: 90px;
            height: 90px;
            font-size: 28px;
        }

        .second .empty-avatar {
            border-color: #C0C0C0;
        }

        .third .empty-avatar {
            border-color: #CD7F32;
        }

        .podium-base {
            width: 120px;
            border-radius: 8px 8px 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 15px;
            color: white;
            font-weight: bold;
            position: relative;
        }

        .first .podium-base {
            height: 140px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
        }

        .second .podium-base {
            height: 120px;
            background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
        }

        .third .podium-base {
            height: 100px;
            background: linear-gradient(135deg, #CD7F32, #B8860B);
        }

        .position-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .student-name {
            font-size: 14px;
            text-align: center;
            margin-bottom: 5px;
        }

        .merit-score {
            font-size: 12px;
            opacity: 0.9;
        }

        .crown {
            position: absolute;
            top: -20px;
            font-size: 24px;
            animation: crownBounce 2s infinite;
        }

        @keyframes crownBounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        /* Rankings Section */
        .rankings-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .section-title.white {
            color: white;
            margin-bottom: 2rem;
        }

        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }

        .refresh-btn {
            background: none;
            border: 2px solid #4f46e5;
            color: #4f46e5;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background: #4f46e5;
            color: white;
        }

        .rankings-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .ranking-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 20px;
            display: grid;
            grid-template-columns: 60px 1fr 120px;
            font-weight: bold;
            gap: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 18px;
            color: #374151;
        }

        .empty-state p {
            font-size: 16px;
            line-height: 1.5;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer-info {
            margin-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 1rem;
            }
            
            .podium-container {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .ranking-header {
                grid-template-columns: 50px 1fr 100px;
                gap: 10px;
                padding: 15px 10px;
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            
            <div class="profile">
                <div class="profile-pic">👨‍💻</div>
                <div class="profile-info">
                    <h3>Ahmad Rahman</h3>
                    <p>Computer Science</p>
                    <div class="student-id">Student ID: 123456</div>
                </div>
            </div>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <span class="nav-icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../chatbox/index.html" class="nav-link">
                            <span class="nav-icon">💬</span>
                            Group Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../RunningPage/index.html" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span>
                            Go Fitness
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <span class="nav-icon">📈</span>
                            Student Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="announcement.php" class="nav-link">
                            <span class="nav-icon">📢</span>
                            Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="usercertificate.php" class="nav-link">
                            <span class="nav-icon">🏆</span>
                            Certificate
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div></div>
                <a href="#" class="view-all-btn">🏆 View All Awards</a>
            </header>

            <section class="welcome-section">
                <h1>Student Merit Rankings <span class="trophy-emoji">🏆</span></h1>
                <p>Track academic excellence and student achievements across all programs.</p>
            </section>

            <section class="podium-section">
                <h2 class="section-title white">Top Performers</h2>
                
                <div class="podium-container">
                    <div class="podium-place second">
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">2</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>

                    <div class="podium-place first">
                        <div class="crown">👑</div>
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">1</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>

                    <div class="podium-place third">
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">3</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rankings-section">
                <h2 class="section-title">Merit Rankings</h2>
                
                <div class="controls">
                    <button class="filter-btn active" onclick="filterRankings('all')">All Students</button>
                    <button class="filter-btn" onclick="filterRankings('semester')">This Semester</button>
                    <button class="filter-btn" onclick="filterRankings('year')">This Year</button>
                    <button class="refresh-btn" onclick="refreshRankings()">🔄 Refresh</button>
                </div>

                <div class="rankings-list">
                    <div class="ranking-header">
                        <div>Rank</div>
                        <div>Student</div>
                        <div>Merit Points</div>
                    </div>
                    <div id="rankings-body">
                        <div class="empty-state">
                            <div class="empty-state-icon">📊</div>
                            <h3>No Rankings Available</h3>
                            <p>Student rankings will appear here once merit points are recorded.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="footer-info">
                © PCC Connect • Student Statistics v1.0
            </div>
        </main>
    </div>

    <script>
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
    </script>
</body>
</html>