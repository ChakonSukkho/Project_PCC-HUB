
<?php
include('../config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submission for profile update
if ($_POST && isset($_POST['update_profile'])) {
    // Profile data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['user_phone'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $address = trim($_POST['user_address'] ?? '');
    $program = trim($_POST['program'] ?? '');
    
    // Handle profile image upload
    $profile_image = '';
    $current_image = $_POST['current_image'] ?? '';
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handle_file_upload(
            $_FILES['profile_image'], 
            '../uploads/profiles/', 
            ['jpg', 'jpeg', 'png', 'gif']
        );
        
        if ($upload_result['success']) {
            $profile_image = $upload_result['filename'];
            
            // Delete old image if exists
            if (!empty($current_image) && file_exists('../uploads/profiles/' . $current_image)) {
                unlink('../uploads/profiles/' . $current_image);
            }
        } else {
            $error = "Image upload failed: " . $upload_result['message'];
        }
    } else {
        // Keep current image if no new image uploaded
        $profile_image = $current_image;
    }
    
    // Validation
    if (empty($full_name) || empty($email)) {
        $error = "Full name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Handle empty date_of_birth
        if (empty($date_of_birth)) {
            $date_of_birth = null;
        }
        
        // Update user profile including image
        $update_sql = "UPDATE users SET 
            user_name = ?, 
            user_email = ?, 
            user_phone = ?, 
            user_address = ?, 
            date_of_birth = ?, 
            program = ?,
            profile_image = ?,
            updated_at = NOW() 
            WHERE user_id = ?";
        
        $stmt = $connect->prepare($update_sql);
        $stmt->bind_param("sssssssi", $full_name, $email, $phone, $address, $date_of_birth, $program, $profile_image, $user_id);
        
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            $stmt->close();
        
            // Refresh user data
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt2 = $connect->prepare($sql);
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $user = $result->fetch_assoc();
            $stmt2->close();
        } else {
            $error = "Failed to update profile. Please try again.";
            error_log("SQL Error: " . $connect->error);
        }
    }
}

// Get user information if not already fetched
if (!isset($user)) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        session_destroy();
        header("Location: ../login.php");
        exit();
    }

    $user = $result->fetch_assoc();
}

// Use correct DB column names with null coalescing
$phone = $user['user_phone'] ?? '';
$date_of_birth = $user['date_of_birth'] ?? '';
$address = $user['user_address'] ?? '';
$program = $user['program'] ?? 'Diploma in Computer Science';
$profile_image = $user['profile_image'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Connect - Profile</title>
    <link rel="stylesheet" href="../assests/css/user-css/profile.css">
    <style>
        /* Additional CSS for image upload */
        .profile-image-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .image-upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .profile-image-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid rgba(255, 255, 255, 0.3);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-image-preview:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .profile-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-image-placeholder {
            font-size: 3rem;
            color: white;
        }
        
        .image-upload-input {
            display: none;
        }
        
        .image-upload-label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: none;
        }
        
        .image-upload-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .image-info {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
        }
        
        .sidebar .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .sidebar .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar .profile-pic .placeholder {
            color: white;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo"> 🎓 PCC Hub</div>

            <!-- Clickable Profile Section -->
            <a href="profile.php" class="profile-link">
                <div class="profile">
                    <div class="profile-pic" id="img-profile">
                        <?php if (!empty($profile_image) && file_exists('../uploads/profiles/' . $profile_image)): ?>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
                        <?php else: ?>
                            <div class="placeholder">👤</div>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($user['user_name']); ?></h3>
                        <div class="student-id">
                            Student ID: <?php echo !empty($user['matric_number']) ? htmlspecialchars($user['matric_number']) : "N/A"; ?>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Navigation Menu -->
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="userdashboard.php" class="nav-link">
                            <span class="nav-icon">📊</span> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../chatbox/index.html" class="nav-link">
                            <span class="nav-icon">💬</span> Group Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="userRunning.php" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span> Go Fitness
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="statis.php" class="nav-link">
                            <span class="nav-icon">📈</span> Student Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="announcement.php" class="nav-link">
                            <span class="nav-icon">📢</span> Announcements
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="usercertificate.php" class="nav-link">
                            <span class="nav-icon">🏆</span> Certificate
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link active">
                            <span class="nav-icon">👤</span> Profile
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Logout -->
            <div class="logout-section">
                <button class="logout-btn" id="logoutBtn">🚪 Logout</button>
            </div>

            <div class="footer-info">
                © PCC Connect • v1.0
            </div>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="back-btn">
                    <a href="userdashboard.php" class="back-link">← Back to Dashboard</a>
                </div>
                <button class="edit-profile-btn" id="editProfileBtn">
                    ✏️ Edit Profile
                </button>
            </header>

            <!-- Welcome Section -->
            <section class="welcome-section">
                <div class="welcome-content">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            <?php if (!empty($profile_image) && file_exists('../uploads/profiles/' . $profile_image)): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="welcome-text">
                        <h1>Welcome back, <?php echo htmlspecialchars($user['user_name']); ?>! <span class="wave-emoji">👋</span></h1>
                        <p>Ready to make today productive? Check your profile updates below.</p>
                        <div class="program-badge">
                            <?php echo !empty($user['program']) ? htmlspecialchars($user['program']) : "Diploma in Computer Science"; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    ✅ <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- FORM WITH ENCTYPE FOR FILE UPLOAD -->
            <form class="profile-form" id="profileForm" method="POST" enctype="multipart/form-data" style="display: contents;">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($profile_image); ?>">
                
                <!-- Profile Image Section -->
                <section class="profile-image-section" id="imageSection">
                    <div class="section-header">
                        <div class="section-icon">📸</div>
                        <h2>Profile Picture</h2>
                    </div>
                    
                    <div class="image-upload-container">
                        <div class="profile-image-preview" onclick="document.getElementById('profile_image').click()">
                            <?php if (!empty($profile_image) && file_exists('../uploads/profiles/' . $profile_image)): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" id="imagePreview">
                            <?php else: ?>
                                <div class="profile-image-placeholder" id="imagePlaceholder">📸</div>
                            <?php endif; ?>
                        </div>
                        
                        <input type="file" id="profile_image" name="profile_image" class="image-upload-input" accept="image/*" onchange="previewImage(this)">
                        
                        <label for="profile_image" class="image-upload-label" id="imageUploadLabel" style="display: none;">
                            📷 Choose Image
                        </label>
                        
                        <div class="image-info">
                            Click the image to change • Max 5MB • JPG, PNG, GIF
                        </div>
                    </div>
                </section>
                
                <!-- Profile Information Section -->
                <section class="profile-section">
                    <div class="section-header">
                        <div class="section-icon">👤</div>
                        <h2>Profile Information</h2>
                    </div>

                    <!-- View Mode -->
                    <div class="profile-view" id="profileView">
                        <div class="profile-grid">
                            <div class="profile-item">
                                <label>Full Name</label>
                                <div class="profile-value"><?php echo htmlspecialchars($user['user_name']); ?></div>
                            </div>
                            
                            <div class="profile-item">
                                <label>Student ID</label>
                                <div class="profile-value"><?php echo !empty($user['matric_number']) ? htmlspecialchars($user['matric_number']) : "N/A"; ?></div>
                            </div>
                            
                            <div class="profile-item">
                                <label>Email Address</label>
                                <div class="profile-value">
                                    <?php echo htmlspecialchars($user['user_email'] ?? 'N/A'); ?>
                                </div>
                            </div>
                            <div class="profile-item">
                                <label>Program / Department</label>
                                <div class="profile-value"><?php echo !empty($user['program']) ? htmlspecialchars($user['program']) : "Diploma in Computer Science"; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div class="profile-edit" id="profileEdit" style="display: none;">
                        <div class="profile-grid">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="student_id">Student ID</label>
                                <input type="text" id="student_id" name="student_id" value="<?php echo !empty($user['matric_number']) ? htmlspecialchars($user['matric_number']) : ''; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                               <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['user_email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="program">Program / Department</label>
                                <input type="text" id="program" name="program" value="<?php echo !empty($user['program']) ? htmlspecialchars($user['program']) : 'Diploma in Computer Science'; ?>">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Personal Information Section -->
                <section class="personal-section">
                    <div class="section-header">
                        <div class="section-icon">📞</div>
                        <h2>Personal Information</h2>
                    </div>

                    <!-- View Mode -->
                    <div class="personal-view" id="personalView">
                        <div class="profile-grid">
                            <div class="profile-item">
                                <label>Phone Number</label>
                                <div class="profile-value">
                                    <?php echo !empty($user['user_phone']) ? htmlspecialchars($user['user_phone']) : "Not set"; ?>
                                </div>
                            </div>
                            
                            <div class="profile-item">
                                <label>Date of Birth</label>
                                <div class="profile-value">
                                    <?php echo !empty($user['date_of_birth']) ? date('d/m/Y', strtotime($user['date_of_birth'])) : "Not set"; ?>
                                </div>
                            </div>
                            
                            <div class="profile-item full-width">
                                <label>Address</label>
                                <div class="profile-value">
                                    <?php echo !empty($user['user_address']) ? htmlspecialchars($user['user_address']) : "Not set"; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div class="personal-edit" id="personalEdit" style="display: none;">
                        <div class="profile-grid">
                            <div class="form-group">
                                <label for="user_phone">Phone Number</label>
                                <input type="tel" id="user_phone" name="user_phone" value="<?php echo htmlspecialchars($user['user_phone'] ?? ''); ?>" placeholder="+60 12-345 6789">
                            </div>
                            
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $user['date_of_birth'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="user_address">Address</label>
                                <input type="text" id="user_address" name="user_address" value="<?php echo htmlspecialchars($user['user_address'] ?? ''); ?>" placeholder="Your address">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Action Buttons -->
                <div class="action-buttons" id="actionButtons" style="display: none;">
                    <button type="submit" name="update_profile" class="save-btn">
                        💾 Save Changes
                    </button>
                    <button type="button" class="cancel-btn" id="cancelBtn">
                        ✖️ Cancel
                    </button>
                </div>

            </form>

            <div class="footer-info">
                © PCC Connect • v1.0
            </div>
        </main>
    </div>

    <script>
        // Image preview function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    const placeholder = document.getElementById('imagePlaceholder');
                    
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        // Create new img element
                        const newImg = document.createElement('img');
                        newImg.id = 'imagePreview';
                        newImg.src = e.target.result;
                        newImg.alt = 'Profile';
                        newImg.style.width = '100%';
                        newImg.style.height = '100%';
                        newImg.style.objectFit = 'cover';
                        
                        // Replace placeholder
                        placeholder.parentNode.replaceChild(newImg, placeholder);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Original profile.js functionality
        const editProfileBtn = document.getElementById('editProfileBtn');
        const profileView = document.getElementById('profileView');
        const profileEdit = document.getElementById('profileEdit');
        const personalView = document.getElementById('personalView');
        const personalEdit = document.getElementById('personalEdit');
        const actionButtons = document.getElementById('actionButtons');
        const cancelBtn = document.getElementById('cancelBtn');
        const imageUploadLabel = document.getElementById('imageUploadLabel');

        editProfileBtn.addEventListener('click', function() {
            // Switch to edit mode
            profileView.style.display = 'none';
            profileEdit.style.display = 'block';
            personalView.style.display = 'none';
            personalEdit.style.display = 'block';
            actionButtons.style.display = 'flex';
            imageUploadLabel.style.display = 'inline-block';
            
            // Update button text
            editProfileBtn.style.display = 'none';
        });

        cancelBtn.addEventListener('click', function() {
            // Switch back to view mode
            profileView.style.display = 'block';
            profileEdit.style.display = 'none';
            personalView.style.display = 'block';
            personalEdit.style.display = 'none';
            actionButtons.style.display = 'none';
            imageUploadLabel.style.display = 'none';
            
            // Reset button
            editProfileBtn.style.display = 'block';
            
            // Reset form
            document.getElementById('profileForm').reset();
        });

        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        });
    </script>
</body>
</html>



<!-- Add this i need to make profile that for user can put own img to the web page, so for the next task i will make profile-img page -->