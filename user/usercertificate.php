<?php
include('../config.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $connect->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}


$user = $result->fetch_assoc();


// Initialize message variables
$message = '';
$error = '';

// Check for session messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// =========================
// Helper Functions
// =========================

// Fetch all certificates for a user
function getUserCertificates($user_id) {
    global $connect;
    $sql = "SELECT uc.*, a.activity_name, a.activity_date, a.activity_location, a.activity_type
            FROM user_certificates uc
            JOIN activities a ON uc.activity_id = a.activity_id
            WHERE uc.user_id = ? AND uc.status = 'active'
            ORDER BY uc.issued_date DESC";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch certificate statistics
function getCertificateStats($user_id) {
    global $connect;
    $stats = [
        'total' => 0,
        'marathon' => 0,
        'challenge' => 0,
        'recent_month' => 0
    ];

    // Total
    $sql = "SELECT COUNT(*) as cnt FROM user_certificates WHERE user_id = ? AND status = 'active'";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stats['total'] = $stmt->get_result()->fetch_assoc()['cnt'];

    // Sports (Marathon)
    $sql = "SELECT COUNT(*) as cnt FROM user_certificates uc
            JOIN activities a ON uc.activity_id = a.activity_id
            WHERE uc.user_id = ? AND uc.status = 'active' AND a.activity_type = 'sports'";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stats['marathon'] = $stmt->get_result()->fetch_assoc()['cnt'];

    // Challenges (academic + leadership + community_service)
    $sql = "SELECT COUNT(*) as cnt FROM user_certificates uc
            JOIN activities a ON uc.activity_id = a.activity_id
            WHERE uc.user_id = ? AND uc.status = 'active' 
            AND a.activity_type IN ('academic', 'leadership', 'community_service')";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stats['challenge'] = $stmt->get_result()->fetch_assoc()['cnt'];

    // Certificates issued this month
    $sql = "SELECT COUNT(*) as cnt FROM user_certificates 
            WHERE user_id = ? AND status = 'active'
            AND MONTH(issued_date) = MONTH(CURDATE()) 
            AND YEAR(issued_date) = YEAR(CURDATE())";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stats['recent_month'] = $stmt->get_result()->fetch_assoc()['cnt'];

    return $stats;
}

// Verify certificate by code
function verifyCertificate($verification_code) {
    global $connect;
    $sql = "SELECT uc.*, u.user_name, u.matric_number, a.activity_name, a.activity_date, a.activity_location
            FROM user_certificates uc
            JOIN users u ON uc.user_id = u.user_id
            JOIN activities a ON uc.activity_id = a.activity_id
            WHERE uc.verification_code = ? AND uc.status = 'active'";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('s', $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

// =========================
// Handle Certificate Actions
// =========================

// Download request
if (isset($_GET['download']) && isset($_GET['cert_id'])) {
    $cert_id = (int)$_GET['cert_id'];
    
    $query = "SELECT uc.*, a.activity_name FROM user_certificates uc
              JOIN activities a ON uc.activity_id = a.activity_id
              WHERE uc.certificate_id = ? AND uc.user_id = ? AND uc.status = 'active'";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('ii', $cert_id, $user_id);
    $stmt->execute();
    $certificate = $stmt->get_result()->fetch_assoc();
    
    if ($certificate) {
        $_SESSION['message'] = 'Certificate download started! (demo only)';
        // Actual download would use:
        // header('Content-Type: application/pdf');
        // header('Content-Disposition: attachment; filename="' . $certificate['certificate_name'] . '.pdf"');
        // readfile($certificate['certificate_file_path']);
        // exit();
    } else {
        $_SESSION['error'] = 'Certificate not found or access denied.';
    }
    header('Location: usercertificate.php');
    exit();
}

// Verification request
$verified_cert = false;
if (isset($_GET['verify']) && isset($_GET['code'])) {
    $verification_code = trim($_GET['code']);
    if (!empty($verification_code)) {
        $verified_cert = verifyCertificate($verification_code);
        if ($verified_cert) {
            $message = 'Certificate verified successfully!';
        } else {
            $error = 'Invalid verification code or certificate not found.';
        }
    } else {
        $error = 'Please enter a verification code.';
    }
}


// Fetch all certificates with detailed information for modal
function getAllCertificatesForModal($user_id) {
    global $connect;
    $sql = "SELECT uc.*, a.activity_name, a.activity_date, a.activity_location, a.activity_type,
            DATE_FORMAT(uc.issued_date, '%Y-%m-%d') as formatted_date
            FROM user_certificates uc
            JOIN activities a ON uc.activity_id = a.activity_id
            WHERE uc.user_id = ? AND uc.status = 'active'
            ORDER BY uc.issued_date DESC";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Add this after your existing data loading section
$all_certificates = getAllCertificatesForModal($user_id);
// =========================
// Auto-generate certificate when task is completed
// =========================

// Example: check if user has completed any activity but no certificate issued yet
$autoCertSQL = "
    SELECT ar.activity_id, a.activity_name, a.activity_date
    FROM activity_registrations ar
    JOIN activities a ON ar.activity_id = a.activity_id
    WHERE ar.user_id = ? 
      AND ar.status = 'completed'
      AND ar.activity_id NOT IN (
          SELECT activity_id FROM user_certificates WHERE user_id = ?
      )
";

$stmt = $connect->prepare($autoCertSQL);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$newCerts = $stmt->get_result();

while ($row = $newCerts->fetch_assoc()) {
    $activity_id = $row['activity_id'];
    $verification_code = strtoupper(bin2hex(random_bytes(4))); // 8-char code
    $certificate_name = "Certificate of Participation - " . $row['activity_name'];
    $certificate_type = "participation";

    $insertSQL = "
        INSERT INTO user_certificates 
        (user_id, activity_id, certificate_name, certificate_type, issued_date, verification_code, status) 
        VALUES (?, ?, ?, ?, NOW(), ?, 'active')
    ";
    $insertStmt = $connect->prepare($insertSQL);
    $insertStmt->bind_param("iisss", $user_id, $activity_id, $certificate_name, $certificate_type, $verification_code);
    $insertStmt->execute();
}

// =========================
// Load Data for Page
// =========================
$certificates = getUserCertificates($user_id);
$cert_stats = getCertificateStats($user_id);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Connect - My Certificates</title>
    <link rel="stylesheet" href="../assests/css/user-css/userCertificate.css">
    <meta name="description" content="View and manage your certificates from PCC Hub activities">
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
                    <h3><?= htmlspecialchars($user['user_name']) ?></h3>
                    <div class="student-id">Student ID: <?= htmlspecialchars($user['matric_number']) ?></div>
                </div>
            </div>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="userdashboard.php" class="nav-link">
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
                        <a href="userRunning.php" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span>
                            Go Fitness
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="statis.php" class="nav-link">
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
                        <a href="usercertificate.php" class="nav-link active">
                            <span class="nav-icon">🏆</span>
                            Certificate
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link">
                            <span class="nav-icon">👤</span>
                            Profile
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="breadcrumb">
                    <a class="breadcrumb-item" href="userdashboard.php">Dashboard</a>
                    <span class="breadcrumb-separator">›</span>
                    <span class="breadcrumb-item active">Certificates</span>
                </div>
                <button class="view-all-btn" onclick="openAllCertificatesModal()">View All Certificates</button>
            </header>

            <section class="welcome-section">
                <h1>Your Certificates <span class="wave-emoji">🏅</span></h1>
                <p>All your achievements are collected here. Download, view, and share your accomplishments.</p>
            </section>

            <!-- Certificate Statistics -->
            <section class="stats-overview">
            <div class="certificates">
               <div class="section-header">
                <h2 class="section-title" id="cert">🎓 My Certificates</        h2>
            </div>

    <?php if ($certificates && $certificates->num_rows > 0): ?>
        <div class="certificates-grid">
            <?php while ($cert = $certificates->fetch_assoc()): ?>
                <div class="certificate-card <?= htmlspecialchars($cert['certificate_type']) ?>">
                    
                    <!-- Header -->
                    <div class="certificate-header">
                        <div>
                            <h3 class="certificate-title"><?= htmlspecialchars($cert['certificate_name']) ?></h3>
                            <p class="certificate-date"><?= date("d M Y", strtotime($cert['issued_date'])) ?></p>
                        </div>
                        <span class="certificate-type"><?= ucfirst($cert['certificate_type']) ?></span>
                    </div>

                    <!-- Achievement Details -->
                    <?php if (!empty($cert['achievement_details'])): ?>
                        <div class="achievement-details">
                            <p><?= htmlspecialchars($cert['achievement_details']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Stats (position, time, code) -->
                    <div class="certificate-stats">
                        <?php if (!empty($cert['position_achieved'])): ?>
                            <div class="cert-stat">
                                <div class="cert-value">#<?= $cert['position_achieved'] ?></div>
                                <div class="cert-label">Position</div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cert['time_achieved'])): ?>
                            <div class="cert-stat">
                                <div class="cert-value"><?= $cert['time_achieved'] ?></div>
                                <div class="cert-label">Time</div>
                            </div>
                        <?php endif; ?>

                        <div class="cert-stat">
                            <div class="cert-value">ID</div>
                            <div class="cert-label"><?= $cert['certificate_code'] ?></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="certificate-actions">
                        <a href="usercertificate_view.php?id=<?= $cert['certificate_id'] ?>" class="btn btn-primary">👁 View</a>
                        <a href="usercertificate_download.php?id=<?= $cert['certificate_id'] ?>" class="btn btn-secondary">⬇ Download</a>
                    </div>

                    <!-- Verification -->
                    <div class="verification-info">
                        <small>Verification Code: <?= $cert['verification_code'] ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <!-- No Certificates -->
        <a href="usercertificate_view.php?id=<?= $cert['certificate_id'] ?>" class="btn btn-primary">👁 View</a>
        <a href="usercertificate_download.php?id=<?= $cert['certificate_id'] ?>" class="btn btn-secondary">⬇ Download</a>

    <?php endif; ?>
</div>

            </section>

            <div class="footer-info">
                © PCC Connect • Certificate Management System v2.0
                <br>
                <small>For certificate verification issues, contact <a href="mailto:admin@pcc.edu.my">admin@pcc.edu.my</a></small>
            </div>
        </main>
    </div>


    <script>
        // Certificate viewing function
        function viewCertificate(certificateId) {
            // In production, this would open a modal or new window with certificate details
            alert('Certificate viewer would open here for Certificate ID: ' + certificateId);
            // Example: window.open('certificate_viewer.php?id=' + certificateId, '_blank', 'width=800,height=600');
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }, 5000);
            });
        });

// Certificate data from PHP
const certificatesData = <?php echo json_encode($all_certificates); ?>;

// Open certificates modal function
function openAllCertificatesModal() {
    const modal = document.getElementById('certificatesModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        loadAllCertificates('all');
    }
}

// Close certificates modal function
function closeAllCertificatesModal() {
    const modal = document.getElementById('certificatesModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Load certificates into modal
function loadAllCertificates(filter = 'all') {
    const grid = document.getElementById('certificatesGrid');
    if (!grid) return;
    
    const filteredCerts = filter === 'all' 
        ? certificatesData 
        : certificatesData.filter(cert => cert.activity_type === filter);

    if (filteredCerts.length === 0) {
        grid.innerHTML = `
            <div class="no-certificates-modal" style="grid-column: 1 / -1;">
                <div class="no-certs-icon">🏆</div>
                <h3 class="no-certs-title">No Certificates Found</h3>
                <p class="no-certs-subtitle">No certificates match the selected filter.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = filteredCerts.map(cert => {
        const certDate = new Date(cert.formatted_date);
        const formattedDate = certDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        return `
            <div class="modal-cert-card" data-type="${cert.activity_type}">
                <div class="cert-type-badge ${cert.activity_type}">${cert.activity_type.replace('_', ' ').toUpperCase()}</div>
                
                <h3 class="modal-cert-title">${cert.certificate_name}</h3>
                <p class="modal-cert-activity">${cert.activity_name}</p>
                
                <div class="modal-cert-details">
                    <div class="cert-detail-item">
                        <span class="cert-detail-icon">📅</span>
                        <span class="cert-detail-text">${formattedDate}</span>
                    </div>
                    <div class="cert-detail-item">
                        <span class="cert-detail-icon">📍</span>
                        <span class="cert-detail-text">${cert.activity_location || 'Not specified'}</span>
                    </div>
                    <div class="cert-detail-item">
                        <span class="cert-detail-icon">🏆</span>
                        <span class="cert-detail-text">${cert.position_achieved ? `Position: ${cert.position_achieved}` : 'Completed'}</span>
                    </div>
                    <div class="cert-detail-item">
                        <span class="cert-detail-icon">🔢</span>
                        <span class="cert-detail-text">${cert.verification_code.slice(-6)}</span>
                    </div>
                </div>
                
                ${cert.achievement_details ? `
                    <div style="margin: 15px 0; padding: 12px; background: #f8faff; border-radius: 8px; border-left: 4px solid #6366f1;">
                        <small style="color: #64748b; font-weight: 500;">${cert.achievement_details}</small>
                    </div>
                ` : ''}
                
                <div class="modal-cert-actions">
                    <button class="modal-btn modal-btn-secondary" onclick="viewCertificate(${cert.certificate_id})">
                        👁️ View
                    </button>
                    <button class="modal-btn modal-btn-primary" onclick="downloadCertificateModal(${cert.certificate_id})">
                        ⬇️ Download
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Download certificate function for modal
function downloadCertificateModal(certId) {
    window.location.href = `?download=1&cert_id=${certId}`;
    // Close modal after starting download
    setTimeout(() => {
        closeAllCertificatesModal();
    }, 1000);
}

// Initialize modal functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Create modal HTML
    createCertificatesModal();
    
    // Setup filter functionality
    setupModalFilters();
    
    // Setup modal close functionality
    setupModalClose();
    
    // Update the "View All Certificates" button to open modal instead
    const viewAllBtn = document.querySelector('.view-all-btn');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openAllCertificatesModal();
        });
    }
});

// Create the modal HTML structure
function createCertificatesModal() {
    const modalHTML = `
        <div id="certificatesModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title">🏆 All Your Certificates</h2>
                    <button class="close-btn" onclick="closeAllCertificatesModal()">×</button>
                </div>
                <div class="modal-content">
                    <div class="filter-section">
                        <span style="font-weight: 600; color: #64748b;">Filter by:</span>
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="sports">Sports</button>
                        <button class="filter-btn" data-filter="academic">Academic</button>
                        <button class="filter-btn" data-filter="leadership">Leadership</button>
                        <button class="filter-btn" data-filter="community_service">Community Service</button>
                    </div>
                    <div id="certificatesGrid" class="certificates-modal-grid"></div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Setup filter functionality
function setupModalFilters() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('filter-btn')) {
            // Remove active class from all filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            e.target.classList.add('active');
            
            // Load certificates with filter
            const filter = e.target.getAttribute('data-filter');
            loadAllCertificates(filter);
        }
    });
}

// Setup modal close functionality
function setupModalClose() {
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('certificatesModal');
        if (e.target === modal) {
            closeAllCertificatesModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('certificatesModal');
            if (modal && modal.classList.contains('active')) {
                closeAllCertificatesModal();
            }
        }
    });
    
    // Prevent modal content clicks from closing modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.modal-container')) {
            e.stopPropagation();
        }
    });
}
    </script>
    <script src="../assets/js/user-js/certificate.js"></script>
</body>
</html>