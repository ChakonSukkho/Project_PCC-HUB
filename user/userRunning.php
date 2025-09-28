<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Hub - Running Tracker</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assests/css/user-css/running.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            
            <a href="userdashboard.php" class="back-button">
                ← Back to Dashboard
            </a>
            
            <div class="profile">
                <div class="profile-pic">🏃‍♂️</div>
                <div class="profile-info">
                    <p>Running Tracker</p>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <section class="welcome-section">
                <h1>Welcome to your Running Tracker, <span id="welcomeUserName">Runner</span>! <span class="runner-emoji">🏃‍♂️</span></h1>
                <p>Track your runs, monitor your progress, and achieve your fitness goals.</p>
            </section>

            <!-- Statistics Overview -->
            <section class="stats-section">
                <h2 class="section-title">📊 Your Running Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🏃</div>
                        <div class="stat-value" id="totalRuns">0</div>
                        <div class="stat-label">Total Runs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📏</div>
                        <div class="stat-value" id="totalDistance">0.0</div>
                        <div class="stat-label">Total Distance (km)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⏱️</div>
                        <div class="stat-value" id="longestRun">0:00</div>
                        <div class="stat-label">Longest Run</div>
                    </div>
                </div>
            </section>

            <!-- Main Tracker -->
            <section class="tracker-card">
                <h2 style="color: #1f2937; margin-bottom: 1rem;">🎯 Current Run Tracker</h2>
                
                <div class="tracker-display">
                    <div class="tracker-metric">
                        <span class="metric-value" id="currentTime">00:00</span>
                        <div class="metric-label">Duration</div>
                    </div>
                    <div class="tracker-metric">
                        <span class="metric-value" id="currentDistance">0.00</span>
                        <div class="metric-label">Distance (km)</div>
                    </div>
                    <div class="tracker-metric">
                        <span class="metric-value" id="currentPace">0:00</span>
                        <div class="metric-label">Pace (/km)</div>
                    </div>
                </div>

                <div class="map-container">
                    <div id="map"></div>
                    <div class="map-overlay" id="mapOverlay" style="display: none;">
                        <div class="map-status">GPS Ready</div>
                    </div>
                </div>

                <div class="tracker-controls">
                    <button class="btn btn-primary" id="startBtn">▶️ Start Run</button>
                    <button class="btn btn-warning" id="pauseBtn" disabled>⏸️ Pause</button>
                    <button class="btn btn-danger" id="stopBtn" disabled>⏹️ Stop Run</button>
                </div>
                
            </section>

            <!-- Run History -->
            <section class="history-section">
                <h2 class="history-title">📚 Run History</h2>
                <div id="runHistory">
                    <div class="empty-state">
                        <div class="empty-state-icon">🏃‍♂️</div>
                        <p>No runs recorded yet. Start your first run to see your history here!</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="loading-indicator" style="display: none;">
        <div class="loading-spinner"></div>
        <p>Loading...</p>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
    
    <!-- Custom Scripts -->
    <script src="js/firebase-config.js"></script>
    <script src="js/running-tracker.js"></script>
    <script src="../assests/user-js/running.js"></script>
</body>
</html>