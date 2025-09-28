<?php
// update_profile.php - Separate handler for profile updates (optional)
session_start();
include('../config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

// Handle profile information update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['user_phone'] ?? '');
        $date_of_birth = $_POST['date_of_birth'] ?? null;
        $address = trim($_POST['user_address'] ?? '');
        $program = trim($_POST['program'] ?? '');
        
        // Validation
        if (empty($full_name)) {
            throw new Exception("Full name is required.");
        }
        
        if (empty($email)) {
            throw new Exception("Email address is required.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        // Check if email is already taken by another user
        $stmt = $connect->prepare("SELECT user_id FROM users WHERE user_email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Email address is already taken by another user.");
        }
        
        // Handle empty date_of_birth
        if (empty($date_of_birth)) {
            $date_of_birth = null;
        }
        
        // Update using correct DB column names
        $update_sql = "UPDATE users SET 
            user_name = ?, 
            user_email = ?, 
            user_phone = ?, 
            user_address = ?, 
            date_of_birth = ?, 
            program = ?,
            updated_at = NOW() 
            WHERE user_id = ?";

        $stmt = $connect->prepare($update_sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $connect->error);
        }
        
        $stmt->bind_param("ssssssi", $full_name, $email, $phone, $address, $date_of_birth, $program, $user_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Profile updated successfully!";
            
            // Log the successful update
            error_log("Profile updated successfully for user ID: " . $user_id);
            
        } else {
            throw new Exception("Failed to execute update: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        
        // Log the error
        error_log("Profile update error for user ID " . $user_id . ": " . $e->getMessage());
    }
    
    // If this is an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Regular form submission - redirect back to profile
    if ($response['success']) {
        header("Location: profile.php?success=" . urlencode($response['message']));
    } else {
        header("Location: profile.php?error=" . urlencode($response['message']));
    }
    exit();
}

// Handle profile image upload only
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    try {
        $uploadDir = '../uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }
        
        $file = $_FILES['profile_image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error occurred: " . $file['error']);
        }
        
        // Get file info
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType = $file['type'];
        
        // Validate file type
        if (!in_array($mimeType, $allowedMimes) || !in_array($fileType, ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
            throw new Exception("Only JPEG, PNG, GIF, and WebP images are allowed.");
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new Exception("File size must be less than 5MB.");
        }
        
        // Validate if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception("File is not a valid image.");
        }
        
        // Generate unique filename
        $newFileName = 'profile_' . $user_id . '_' . time() . '.' . $fileType;
        $uploadPath = $uploadDir . $newFileName;
        
        // Get current profile image to delete old one
        $stmt = $connect->prepare("SELECT profile_image FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentUser = $result->fetch_assoc();
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload image. Please try again.");
        }
        
        // Update database with new profile image
        $stmt = $connect->prepare("UPDATE users SET profile_image = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("si", $newFileName, $user_id);
        
        if (!$stmt->execute()) {
            // If database update fails, delete the uploaded file
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            throw new Exception("Database error: " . $stmt->error);
        }
        
        // Delete old profile image if exists and update was successful
        if (!empty($currentUser['profile_image']) && 
            file_exists($uploadDir . $currentUser['profile_image']) && 
            $currentUser['profile_image'] !== $newFileName) {
            unlink($uploadDir . $currentUser['profile_image']);
        }
        
        $response['success'] = true;
        $response['message'] = "Profile picture updated successfully!";
        $response['image_url'] = '../uploads/profiles/' . $newFileName;
        
        // Log successful upload
        error_log("Profile image updated successfully for user ID: " . $user_id . " - File: " . $newFileName);
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        
        // Log the error
        error_log("Profile image upload error for user ID " . $user_id . ": " . $e->getMessage());
    }
    
    // If this is an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Regular form submission - redirect back to profile
    if ($response['success']) {
        header("Location: profile.php?success=" . urlencode($response['message']));
    } else {
        header("Location: profile.php?error=" . urlencode($response['message']));
    }
    exit();
}

// If no valid POST data, redirect to profile
else {
    header("Location: profile.php");
    exit();
}
?>