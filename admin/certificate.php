<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Management Dashboard - PCC Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }

        .header h1 {
            color: #1e40af;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .action-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .action-icon {
            font-size: 32px;
            margin-right: 15px;
        }

        .action-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e40af;
        }

        .action-description {
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .btn {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .recent-activities {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 20px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-info {
            flex: 1;
        }

        .activity-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .activity-details {
            font-size: 14px;
            color: #64748b;
        }

        .activity-actions {
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .quick-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .quick-action-btn {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
            transform: translateY(-2px);
        }

        .quick-action-icon {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <h1>🏆 Certificate Management Dashboard</h1>
            <p>Manage certificates, generate new ones, and track student achievements</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">📜</span>
                <div class="stat-number" id="totalCertificates">0</div>
                <div class="stat-label">Total Certificates</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📅</span>
                <div class="stat-number" id="thisMonthCertificates">0</div>
                <div class="stat-label">This Month</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">⏳</span>
                <div class="stat-number" id="pendingCertificates">0</div>
                <div class="stat-label">Pending Generation</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">✅</span>
                <div class="stat-number" id="verifiedCertificates">0</div>
                <div class="stat-label">Recently Verified</div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="actions-grid">
            <div class="action-card">
                <div class="action-header">
                    <span class="action-icon">🎯</span>
                    <h3 class="action-title">Generate Single Certificate</h3>
                </div>
                <p class="action-description">Create a certificate for a specific student and activity.</p>
                <button class="btn" onclick="openGenerateModal()">Generate Certificate</button>
            </div>

            <div class="action-card">
                <div class="action-header">
                    <span class="action-icon">🚀</span>
                    <h3 class="action-title">Bulk Generation</h3>
                </div>
                <p class="action-description">Generate certificates for all participants of an activity at once.</p>
                <a href="bulk_generate_certificates.php" class="btn btn-success">Bulk Generate</a>
            </div>

            <div class="action-card">
                <div class="action-header">
                    <span class="action-icon">🔍</span>
                    <h3 class="action-title">Certificate Verification</h3>
                </div>
                <p class="action-description">Verify certificate authenticity using verification codes.</p>
                <button class="btn btn-warning" onclick="openVerifyModal()">Verify Certificate</button>
            </div>

            <div class="action-card">
                <div class="action-header">
                    <span class="action-icon">📊</span>
                    <h3 class="action-title">Analytics & Reports</h3>
                </div>
                <p class="action-description">View detailed reports and analytics about certificate distribution.</p>
                <button class="btn btn-danger" onclick="showAnalytics()">View Reports</button>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="recent-activities">
            <h2 class="section-title">Recent Certificate Activities</h2>
            <ul class="activity-list" id="recentActivities">
                <li class="activity-item">
                    <div class="activity-info">
                        <div class="activity-name">Loading recent activities...</div>
                        <div class="activity-details">Please wait...</div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-action-grid">
                <a href="#" class="quick-action-btn" onclick="downloadTemplate()">
                    <span class="quick-action-icon">📄</span>
                    <div>Download Template</div>
                </a>
                <a href="#" class="quick-action-btn" onclick="viewLogs()">
                    <span class="quick-action-icon">📋</span>
                    <div>View Logs</div>
                </a>
                <a href="#" class="quick-action-btn" onclick="manageSettings()">
                    <span class="quick-action-icon">⚙️</span>
                    <div>Settings</div>
                </a>
                <a href="#" class="quick-action-btn" onclick="exportData()">
                    <span class="quick-action-icon">📤</span>
                    <div>Export Data</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Generate Certificate Modal -->
    <div class="modal" id="generateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Generate Certificate</h3>
                <button class="close-btn" onclick="closeModal('generateModal')">&times;</button>
            </div>
            <form id="generateForm">
                <div class="form-group">
                    <label for="studentSelect">Select Student:</label>
                    <select id="studentSelect" required>
                        <option value="">Choose a student...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="activitySelect">Select Activity:</label>
                    <select id="activitySelect" required>
                        <option value="">Choose an activity...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="certificateType">Certificate Type:</label>
                    <select id="certificateType" required>
                        <option value="participation">Participation</option>
                        <option value="completion">Completion</option>
                        <option value="achievement">Achievement</option>
                        <option value="excellence">Excellence</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="achievementDetails">Achievement Details (Optional):</label>
                    <textarea id="achievementDetails" rows="3" placeholder="Describe the achievement..."></textarea>
                </div>
                <div class="form-group">
                    <label for="position">Position (Optional):</label>
                    <input type="number" id="position" min="1" placeholder="1, 2, 3...">
                </div>
                <div class="form-group">
                    <label for="timeAchieved">Time/Score (Optional):</label>
                    <input type="text" id="timeAchieved" placeholder="e.g., 15:30, 95 points">
                </div>
                <button type="submit" class="btn">Generate Certificate</button>
            </form>
        </div>
    </div>

    <!-- Verify Certificate Modal -->
    <div class="modal" id="verifyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Verify Certificate</h3>
                <button class="close-btn" onclick="closeModal('verifyModal')">&times;</button>
            </div>
            <form id="verifyForm">
                <div class="form-group">
                    <label for="verificationCode">Verification Code:</label>
                    <input type="text" id="verificationCode" required placeholder="Enter verification code...">
                </div>
                <button type="submit" class="btn btn-warning">Verify Certificate</button>
            </form>
            <div id="verificationResult"></div>
        </div>
    </div>

    <script>
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadRecentActivities();
            loadStudentsAndActivities();
        });

        // Load statistics (simulated data - replace with actual API calls)
        function loadStatistics() {
            // Simulate loading statistics
            setTimeout(() => {
                document.getElementById('totalCertificates').textContent = '1,247';
                document.getElementById('thisMonthCertificates').textContent = '89';
                document.getElementById('pendingCertificates').textContent = '12';
                document.getElementById('verifiedCertificates').textContent = '34';
            }, 500);
        }

        // Load recent activities
        function loadRecentActivities() {
            const activities = [
                {
                    name: 'Certificate generated for John Doe',
                    details: 'Marathon Competition - 3rd Place',
                    time: '2 hours ago',
                    id: 'cert_123'
                },
                {
                    name: 'Bulk generation completed',
                    details: 'Programming Workshop - 25 certificates',
                    time: '5 hours ago',
                    id: 'bulk_456'
                },
                {
                    name: 'Certificate verified',
                    details: 'Jane Smith - Leadership Training',
                    time: '1 day ago',
                    id: 'verify_789'
                }
            ];

            const container = document.getElementById('recentActivities');
            container.innerHTML = activities.map(activity => `
                <li class="activity-item">
                    <div class="activity-info">
                        <div class="activity-name">${activity.name}</div>
                        <div class="activity-details">${activity.details} • ${activity.time}</div>
                    </div>
                    <div class="activity-actions">
                        <button class="btn btn-small" onclick="viewActivity('${activity.id}')">View</button>
                    </div>
                </li>
            `).join('');
        }

        // Load students and activities for dropdowns
        function loadStudentsAndActivities() {
            // Simulate loading data
            const students = [
                { id: 1, name: 'John Doe', matric: 'A12345' },
                { id: 2, name: 'Jane Smith', matric: 'B67890' },
                { id: 3, name: 'Mike Johnson', matric: 'C11111' }
            ];

            const activities = [
                { id: 1, name: 'Marathon Competition', date: '2024-09-15' },
                { id: 2, name: 'Programming Workshop', date: '2024-09-20' },
                { id: 3, name: 'Leadership Training', date: '2024-09-25' }
            ];

            const studentSelect = document.getElementById('studentSelect');
            const activitySelect = document.getElementById('activitySelect');

            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = `${student.name} (${student.matric})`;
                studentSelect.appendChild(option);
            });

            activities.forEach(activity => {
                const option = document.createElement('option');
                option.value = activity.id;
                option.textContent = `${activity.name} (${activity.date})`;
                activitySelect.appendChild(option);
            });
        }

        // Modal functions
        function openGenerateModal() {
            document.getElementById('generateModal').style.display = 'block';
        }

        function openVerifyModal() {
            document.getElementById('verifyModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Form submissions
        document.getElementById('generateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                student_id: document.getElementById('studentSelect').value,
                activity_id: document.getElementById('activitySelect').value,
                certificate_type: document.getElementById('certificateType').value,
                achievement_details: document.getElementById('achievementDetails').value,
                position: document.getElementById('position').value,
                time_achieved: document.getElementById('timeAchieved').value
            };

            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Generating...';
            submitBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                alert('Certificate generated successfully!');
                closeModal('generateModal');
                this.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                loadRecentActivities();
                loadStatistics();
            }, 2000);
        });

        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('verificationCode').value;
            const resultDiv = document.getElementById('verificationResult');

            // Simulate verification
            setTimeout(() => {
                if (code.length >= 8) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <strong>✅ Certificate Verified!</strong><br>
                            Student: John Doe<br>
                            Activity: Marathon Competition<br>
                            Issued: September 15, 2024
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-error">
                            <strong>❌ Invalid Code</strong><br>
                            The verification code you entered is not valid.
                        </div>
                    `;
                }
            }, 1000);
        });

        // Quick action functions
        function viewActivity(id) {
            alert(`Viewing activity: ${id}`);
        }

        function downloadTemplate() {
            alert('Certificate template download started!');
        }

        function viewLogs() {
            alert('Opening system logs...');
        }

        function manageSettings() {
            alert('Opening certificate settings...');
        }

        function exportData() {
            alert('Exporting certificate data...');
        }

        function showAnalytics() {
            alert('Opening analytics dashboard...');
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>