<?php
include('../config.php');

if (!isset($_SESSION['staff_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$message = '';
$error = '';

// Handle form submission
if ($_POST && isset($_POST['update_profile'])) {
    $staff_name  = trim($_POST['staff_name']);
    $staff_email = trim($_POST['staff_email']);
    $staff_phone = trim($_POST['staff_phone']);
    $staff_department = trim($_POST['staff_department']);

    if (empty($staff_name) || empty($staff_email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($staff_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $update_sql = "UPDATE staff SET 
            staff_name = ?, 
            staff_email = ?, 
            staff_phone = ?, 
            staff_department = ?, 
            updated_at = NOW()
            WHERE staff_id = ?";
        
        $stmt = $connect->prepare($update_sql);
        $stmt->bind_param("ssssi", $staff_name, $staff_email, $staff_phone, $staff_department, $staff_id);

        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Get staff info
$sql = "SELECT * FROM staff WHERE staff_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$staff = $result->fetch_assoc();
$staff_phone = $staff['staff_phone'] ?? '';
$staff_department = $staff['staff_department'] ?? 'General Department';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Hub - Staff Profile</title>
    <link rel="stylesheet" href="../assests/css/user-css/profile.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>

    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>

            <!-- Profile -->
            <a href="staffProfile.php" class="profile-link">
                <div class="profile">
                    <div class="profile-pic">👨‍🏫</div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($staff['staff_name']); ?></h3>
                        <div class="staff-id">
                            Staff ID: <?php echo htmlspecialchars($staff['staff_id']); ?>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Navigation -->
            <nav>
                <ul class="nav-menu">
                    <li><a href="staffDashboard.php" class="nav-link"><span class="nav-icon">📊</span> Dashboard</a></li>
                    <li><a href="staffActivities.php" class="nav-link"><span class="nav-icon">📅</span> Activities</a></li>
                    <li><a href="staffVerify.php" class="nav-link"><span class="nav-icon">✅</span> Verify Merits</a></li>
                    <li><a href="staffAnnouncement.php" class="nav-link"><span class="nav-icon">📢</span> Announcements</a></li>
                    <li><a href="staffProfile.php" class="nav-link active"><span class="nav-icon">👤</span> Profile</a></li>
                </ul>
            </nav>

            <div class="logout-section">
                <button class="logout-btn" id="logoutBtn">🚪 Logout</button>
            </div>

            <div class="footer-info">© PCC Hub • v1.0</div>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="back-btn">
                    <a href="staffDashboard.php" class="back-link">← Back to Dashboard</a>
                </div>
                <button class="edit-profile-btn" id="editProfileBtn">✏️ Edit Profile</button>
            </header>

            <!-- Welcome -->
            <section class="welcome-section">
                <div class="welcome-content">
                    <div class="profile-avatar"><div class="avatar-circle">👨‍🏫</div></div>
                    <div class="welcome-text">
                        <h1>Welcome back, <?php echo htmlspecialchars($staff['staff_name']); ?>! 👋</h1>
                        <p>Here you can update your staff profile details.</p>
                        <div class="program-badge"><?php echo htmlspecialchars($staff_department); ?></div>
                    </div>
                </div>
            </section>

            <!-- Messages -->
            <?php if ($message): ?><div class="alert alert-success">✅ <?php echo htmlspecialchars($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <!-- Profile Info -->
            <section class="profile-section">
                <div class="section-header"><div class="section-icon">👤</div><h2>Profile Information</h2></div>

                <div class="profile-view" id="profileView">
                    <div class="profile-grid">
                        <div class="profile-item"><label>Full Name</label><div class="profile-value"><?php echo htmlspecialchars($staff['staff_name']); ?></div></div>
                        <div class="profile-item"><label>Staff ID</label><div class="profile-value"><?php echo htmlspecialchars($staff['staff_id']); ?></div></div>
                        <div class="profile-item"><label>Email</label><div class="profile-value"><?php echo htmlspecialchars($staff['staff_email']); ?></div></div>
                        <div class="profile-item"><label>Department</label><div class="profile-value"><?php echo htmlspecialchars($staff_department); ?></div></div>
                    </div>
                </div>

                <form class="profile-edit" id="profileEdit" method="POST" style="display:none;">
                    <div class="profile-grid">
                        <div class="form-group"><label for="staff_name">Full Name</label><input type="text" name="staff_name" value="<?php echo htmlspecialchars($staff['staff_name']); ?>" required></div>
                        <div class="form-group"><label for="staff_id">Staff ID</label><input type="text" name="staff_id" value="<?php echo htmlspecialchars($staff['staff_id']); ?>" readonly></div>
                        <div class="form-group"><label for="staff_email">Email</label><input type="email" name="staff_email" value="<?php echo htmlspecialchars($staff['staff_email']); ?>" required></div>
                        <div class="form-group"><label for="staff_department">Department</label><input type="text" name="staff_department" value="<?php echo htmlspecialchars($staff_department); ?>"></div>
                    </div>
                </form>
            </section>

            <!-- Personal Info -->
            <section class="personal-section">
                <div class="section-header"><div class="section-icon">📞</div><h2>Personal Information</h2></div>

                <div class="personal-view" id="personalView">
                    <div class="profile-grid">
                        <div class="profile-item"><label>Phone</label><div class="profile-value"><?php echo !empty($staff['staff_phone']) ? htmlspecialchars($staff['staff_phone']) : "+60 12-345 6789"; ?></div></div>
                    </div>
                </div>

                <div class="personal-edit" id="personalEdit" style="display:none;">
                    <div class="profile-grid">
                        <div class="form-group"><label for="staff_phone">Phone</label><input type="text" name="staff_phone" value="<?php echo htmlspecialchars($staff_phone); ?>"></div>
                    </div>
                </div>
            </section>

            <!-- Buttons -->
            <div class="action-buttons" id="actionButtons" style="display:none;">
                <button type="submit" name="update_profile" form="profileEdit" class="save-btn">💾 Save Changes</button>
                <button type="button" class="cancel-btn" id="cancelBtn">✖ Cancel</button>
            </div>

            <div class="footer-info">© PCC Hub • v1.0</div>
        </main>
    </div>

    <script src="../assests/js/user-js/profile.js"></script>
</body>
</html>
