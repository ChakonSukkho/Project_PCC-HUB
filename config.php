<?php
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection configuration
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'pcc_hub',
    'charset' => 'utf8mb4'
];

// Create database connection with error handling
try {
    $connect = new mysqli(
        $db_config['host'], 
        $db_config['username'], 
        $db_config['password'], 
        $db_config['database']
    );
    
    // Check connection
    if ($connect->connect_error) {
        throw new Exception("Database connection failed: " . $connect->connect_error);
    }
    
    // Set charset
    $connect->set_charset($db_config['charset']);
    
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database Error: " . $e->getMessage());
    die("Sorry, we're experiencing technical difficulties. Please try again later.");
}

// Application settings
$app_config = [
    'base_url' => 'http://localhost/pcc_hub',
    'title' => 'PCC Hub - Student Co-Curricular System',
    'timezone' => 'Asia/Kuala_Lumpur',
    'upload_path' => '../uploads/',
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif'],
    'allowed_doc_types' => ['pdf', 'doc', 'docx']
];

// Set timezone
date_default_timezone_set($app_config['timezone']);

// =============================
// Utility Functions
// =============================

/**
 * Sanitize input data
 * @param mixed $data The data to sanitize
 * @param mysqli $connection Database connection
 * @return string Sanitized data
 */
function sanitize_input($data, $connection) {
    if (is_null($data)) {
        return '';
    }
    return $connection->real_escape_string(trim(strip_tags($data)));
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Malaysian format)
 * @param string $phone
 * @return bool
 */
function validate_phone($phone) {
    // Remove all non-digits
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check Malaysian phone format (10-11 digits starting with 01 or 6)
    return preg_match('/^(01[0-9]{8,9}|6[0-9]{9,10})$/', $phone);
}

/**
 * Generate random code with prefix
 * @param string $prefix
 * @param int $length
 * @return string
 */
function generate_random_code($prefix = '', $length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = $prefix;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

/**
 * Generate verification code for certificates
 * @param string $type Activity type
 * @param int $year
 * @param string $user_name
 * @return string
 */
function generate_verification_code($type = 'ACT', $year = null, $user_name = '') {
    if (!$year) {
        $year = date('Y');
    }
    $user_initial = strtoupper(substr($user_name, 0, 3));
    $random = generate_random_code('', 6);
    return "VER-{$type}-{$year}-{$user_initial}-{$random}";
}

/**
 * Hash password securely
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Redirect to login if not authenticated
 * @param string $user_type 'user' or 'admin'
 */
function require_login($user_type = 'user') {
    if ($user_type === 'admin' && !is_admin_logged_in()) {
        header('Location: ../admin/login.php');
        exit();
    } elseif ($user_type === 'user' && !is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Handle file upload with validation
 * @param array $file $_FILES array element
 * @param string $upload_dir Directory to upload to
 * @param array $allowed_types Allowed file extensions
 * @return array Result array with success/error info
 */
function handle_file_upload($file, $upload_dir, $allowed_types) {
    global $app_config;
    
    $result = ['success' => false, 'message' => '', 'filename' => ''];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['message'] = 'No file uploaded';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Upload error: ' . $file['error'];
        return $result;
    }
    
    // Check file size
    if ($file['size'] > $app_config['max_file_size']) {
        $result['message'] = 'File too large. Maximum size: ' . ($app_config['max_file_size'] / 1024 / 1024) . 'MB';
        return $result;
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check allowed file types
    if (!in_array($file_extension, $allowed_types)) {
        $result['message'] = 'Invalid file type. Allowed: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Generate unique filename
    $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_name;
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $result['success'] = true;
        $result['filename'] = $unique_name;
        $result['message'] = 'File uploaded successfully';
    } else {
        $result['message'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

// =============================
// Input Sanitization with Validation
// =============================

// Helper function to get and sanitize POST data
function get_post_data($key, $default = '', $validate_function = null) {
    global $connect;
    
    $value = isset($_POST[$key]) ? sanitize_input($_POST[$key], $connect) : $default;
    
    if ($validate_function && !empty($value)) {
        if (!$validate_function($value)) {
            return false; // Validation failed
        }
    }
    
    return $value;
}

// Users (Students) - with validation
$user_name = get_post_data('user_name');
$user_email = get_post_data('user_email', '', 'validate_email');
$user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
$matric_number = get_post_data('matric_number');
$user_phone = get_post_data('user_phone', '', 'validate_phone');
$user_address = get_post_data('user_address');
$date_of_birth = get_post_data('date_of_birth');
$program = get_post_data('program');

// Admins - with validation
$admin_name = get_post_data('admin_name');
$admin_email = get_post_data('admin_email', '', 'validate_email');
$admin_password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';
$staff_id = get_post_data('staff_id');
$admin_phone = get_post_data('admin_phone', '', 'validate_phone');
$admin_role = get_post_data('admin_role');

// Activities
$activity_name = get_post_data('activity_name');
$activity_type = get_post_data('activity_type');
$activity_date = get_post_data('activity_date');    
$activity_time = get_post_data('activity_time');
$activity_location = get_post_data('activity_location');
$activity_description = get_post_data('activity_description');
$max_participants = (int) get_post_data('max_participants', 0);
$registration_deadline = get_post_data('registration_deadline');

// Announcements
$announcement_title = get_post_data('announcement_title');
$announcement_content = get_post_data('announcement_content');
$announcement_category = get_post_data('announcement_category');
$announcement_priority = get_post_data('announcement_priority');

// Certificates
$certificate_name = get_post_data('certificate_name');
$certificate_type = get_post_data('certificate_type');
$achievement_details = get_post_data('achievement_details');
$position_achieved = (int) get_post_data('position_achieved', 0);

// =============================
// Security Headers
// =============================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// =============================
// Error and Success Messages
// =============================
function set_message($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function get_message() {
    if (isset($_SESSION['message'])) {
        $message = [
            'text' => $_SESSION['message'],
            'type' => $_SESSION['message_type'] ?? 'info'
        ];
        unset($_SESSION['message'], $_SESSION['message_type']);
        return $message;
    }
    return null;
}

// =============================
// Database Helper Functions
// =============================

/**
 * Execute prepared statement safely
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i, d, s, b)
 * @param array $params Parameters array
 * @return mysqli_result|bool
 */
function execute_query($sql, $types = '', $params = []) {
    global $connect;
    
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $connect->error);
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $result = $stmt->execute();
    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    $stmt_result = $stmt->get_result();
    $stmt->close();
    
    return $stmt_result;
}

// =============================
// Activity Type Definitions
// =============================
$activity_types = [
    'sports' => 'Sports & Recreation',
    'cultural' => 'Cultural Activities',
    'academic' => 'Academic Programs',
    'community_service' => 'Community Service',
    'leadership' => 'Leadership Development',
    'other' => 'Other Activities'
];

$certificate_types = [
    'participation' => 'Participation',
    'completion' => 'Completion',
    'achievement' => 'Achievement',
    'winner' => 'Winner',
    'runner_up' => 'Runner Up'
];

$announcement_categories = [
    'general' => 'General',
    'academic' => 'Academic',
    'co_curricular' => 'Co-Curricular',
    'urgent' => 'Urgent',
    'event' => 'Event'
];

// =============================
// Constants
// =============================
define('BASE_URL', $app_config['base_url']);
define('UPLOAD_PATH', $app_config['upload_path']);
define('MAX_FILE_SIZE', $app_config['max_file_size']);

// Backward compatibility
$link = BASE_URL;
$title = $app_config['title'];


// Upload img
$image = $connect->real_escape_string(isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '');

?>