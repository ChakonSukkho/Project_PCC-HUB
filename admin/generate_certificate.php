<?php
// ===========================
// admin/generate_certificate.php - Improved Version
// ===========================

require __DIR__ . '/../fpdf185/fpdf.php';
include(__DIR__ . '/../config.php');

// Enhanced FPDF class for better certificate design
class CertificatePDF extends FPDF {
    
    private $logoPath;
    private $certificateData;
    
    public function __construct($logoPath = null, $certificateData = null) {
        parent::__construct('L', 'mm', 'A4'); // Landscape A4
        $this->logoPath = $logoPath;
        $this->certificateData = $certificateData;
    }
    
    // Header with decorative elements
    function Header() {
        // Decorative border
        $this->SetDrawColor(30, 64, 175); // Blue
        $this->SetLineWidth(3);
        $this->Rect(5, 5, 287, 200);
        
        // Inner decorative border
        $this->SetDrawColor(212, 175, 55); // Gold
        $this->SetLineWidth(1);
        $this->Rect(15, 15, 267, 180);
        
        // Corner decorations
        $this->drawCornerDecorations();
    }
    
    // Draw decorative corners
    private function drawCornerDecorations() {
        $this->SetDrawColor(212, 175, 55);
        $this->SetLineWidth(0.5);
        
        // Top corners
        for ($i = 0; $i < 3; $i++) {
            $this->Line(20 + $i*2, 20, 30 + $i*2, 20);
            $this->Line(20 + $i*2, 25, 30 + $i*2, 25);
            $this->Line(252 - $i*2, 20, 262 - $i*2, 20);
            $this->Line(252 - $i*2, 25, 262 - $i*2, 25);
        }
        
        // Bottom corners
        for ($i = 0; $i < 3; $i++) {
            $this->Line(20 + $i*2, 175, 30 + $i*2, 175);
            $this->Line(20 + $i*2, 180, 30 + $i*2, 180);
            $this->Line(252 - $i*2, 175, 262 - $i*2, 175);
            $this->Line(252 - $i*2, 180, 262 - $i*2, 180);
        }
    }
    
    // Add logo with error handling
    public function addLogo($logoPath, $x, $y, $width) {
        if ($logoPath && file_exists($logoPath)) {
            try {
                $this->Image($logoPath, $x, $y, $width);
            } catch (Exception $e) {
                // If logo fails, add text placeholder
                $this->SetXY($x, $y);
                $this->SetFont('Arial', 'B', 16);
                $this->SetTextColor(30, 64, 175);
                $this->Cell($width, 20, 'PCC HUB', 1, 0, 'C');
            }
        }
    }
    
    // Add watermark
    public function addWatermark($text = 'PCC HUB') {
        $this->SetFont('Arial', 'B', 60);
        $this->SetTextColor(240, 240, 240);
        $this->SetXY(50, 80);
        $this->Rotate(45, 148.5, 105);
        $this->Cell(200, 40, $text, 0, 0, 'C');
        $this->Rotate(0);
    }
    
    // Rotation function for watermark
    function Rotate($angle, $x=-1, $y=-1) {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
    
    function _endpage() {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
    
    var $angle=0;
}

// Input validation and sanitization
function validateAndSanitizeInput() {
    $cert_id = filter_input(INPUT_GET, 'cert_id', FILTER_VALIDATE_INT);
    
    if (!$cert_id || $cert_id <= 0) {
        return [
            'success' => false,
            'error' => 'Invalid certificate ID. Please provide a valid certificate ID.'
        ];
    }
    
    return [
        'success' => true,
        'cert_id' => $cert_id
    ];
}

// Database operations
function getCertificateData($cert_id, $connect) {
    $sql = "
        SELECT 
            uc.*,
            u.user_name AS full_name,
            u.matric_number,
            a.activity_name,
            a.activity_date,
            a.activity_location,
            a.activity_type,
            a.description as activity_description
        FROM user_certificates uc
        JOIN users u ON uc.user_id = u.user_id
        JOIN activities a ON uc.activity_id = a.activity_id
        WHERE uc.certificate_id = ? AND uc.status = 'active'
    ";
    
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Database preparation failed: ' . $connect->error
        ];
    }
    
    $stmt->bind_param("i", $cert_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if (!$data) {
        return [
            'success' => false,
            'error' => "Certificate not found for ID: {$cert_id}"
        ];
    }
    
    return [
        'success' => true,
        'data' => $data
    ];
}

// File system operations
function ensureDirectoryExists($dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            return [
                'success' => false,
                'error' => "Failed to create directory: {$dir}"
            ];
        }
    }
    return ['success' => true];
}

function findLogo() {
    $logoCandidates = [
        __DIR__ . '/../assets/images/logo.png',
        __DIR__ . '/../assests/images/logo.png',
        __DIR__ . '/../assets/images/pcc_logo.png',
        __DIR__ . '/../assests/images/pcc_logo.png',
        __DIR__ . '/../assets/logo.png',
        __DIR__ . '/../assests/logo.png'
    ];
    
    foreach ($logoCandidates as $candidate) {
        if (file_exists($candidate)) {
            return $candidate;
        }
    }
    
    return null;
}

// Certificate generation
function generateCertificatePDF($data, $logoPath, $uploadDir) {
    try {
        $pdf = new CertificatePDF($logoPath, $data);
        $pdf->AddPage();
        
        // Add watermark
        $pdf->addWatermark();
        
        // Add logo
        if ($logoPath) {
            $pdf->addLogo($logoPath, 25, 30, 35);
        }
        
        // Institution name
        $pdf->SetXY(70, 35);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(30, 64, 175);
        $pdf->Cell(0, 8, 'POLITEKNIK CHUKAI KEMAMAN', 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, 'Certificate of Achievement', 0, 1, 'L');
        
        // Main title
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 32);
        $pdf->SetTextColor(30, 64, 175);
        
        $certificateTitle = 'CERTIFICATE OF ';
        switch(strtolower($data['certificate_type'])) {
            case 'completion':
                $certificateTitle .= 'COMPLETION';
                break;
            case 'achievement':
                $certificateTitle .= 'ACHIEVEMENT';
                break;
            case 'excellence':
                $certificateTitle .= 'EXCELLENCE';
                break;
            default:
                $certificateTitle .= 'PARTICIPATION';
        }
        
        $pdf->Cell(0, 15, $certificateTitle, 0, 1, 'C');
        
        // Decorative line
        $pdf->SetDrawColor(212, 175, 55);
        $pdf->SetLineWidth(2);
        $pdf->Line(80, $pdf->GetY() + 5, 217, $pdf->GetY() + 5);
        
        // "This is to certify that" text
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'I', 16);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(0, 8, 'This is to certify that', 0, 1, 'C');
        
        // Student name
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor(212, 175, 55);
        $studentName = !empty($data['full_name']) ? $data['full_name'] : 'Student';
        $pdf->Cell(0, 12, strtoupper($studentName), 0, 1, 'C');
        
        // Student ID
        if (!empty($data['matric_number'])) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(0, 6, 'Student ID: ' . $data['matric_number'], 0, 1, 'C');
        }
        
        // Achievement text
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 16);
        $pdf->SetTextColor(60, 60, 60);
        
        $achievementText = 'has successfully participated in';
        if (!empty($data['position_achieved']) && $data['position_achieved'] <= 3) {
            $positions = ['', '1st', '2nd', '3rd'];
            $achievementText = 'has achieved ' . $positions[$data['position_achieved']] . ' place in';
        }
        
        $pdf->Cell(0, 8, $achievementText, 0, 1, 'C');
        
        // Activity name
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(30, 64, 175);
        $activityName = !empty($data['activity_name']) ? $data['activity_name'] : 'Activity';
        
        // Handle long activity names
        if (strlen($activityName) > 40) {
            $pdf->SetFont('Arial', 'B', 16);
        }
        
        $pdf->Cell(0, 10, $activityName, 0, 1, 'C');
        
        // Activity details
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(80, 80, 80);
        
        if (!empty($data['activity_date'])) {
            $activityDate = date('F j, Y', strtotime($data['activity_date']));
            $pdf->Cell(0, 6, 'Held on: ' . $activityDate, 0, 1, 'C');
        }
        
        if (!empty($data['activity_location'])) {
            $pdf->Cell(0, 6, 'At: ' . $data['activity_location'], 0, 1, 'C');
        }
        
        // Achievement details
        if (!empty($data['achievement_details'])) {
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->SetTextColor(100, 100, 100);
            
            // Handle long achievement details
            $achievementDetails = $data['achievement_details'];
            if (strlen($achievementDetails) > 80) {
                $words = explode(' ', $achievementDetails);
                $line1 = implode(' ', array_slice($words, 0, ceil(count($words)/2)));
                $line2 = implode(' ', array_slice($words, ceil(count($words)/2)));
                $pdf->Cell(0, 5, $line1, 0, 1, 'C');
                $pdf->Cell(0, 5, $line2, 0, 1, 'C');
            } else {
                $pdf->Cell(0, 5, $achievementDetails, 0, 1, 'C');
            }
        }
        
        // Time achieved (for sports/competitions)
        if (!empty($data['time_achieved'])) {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(30, 64, 175);
            $pdf->Cell(0, 5, 'Time: ' . $data['time_achieved'], 0, 1, 'C');
        }
        
        // Date and verification
        $pdf->Ln(12);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(120, 120, 120);
        $issuedDate = !empty($data['issued_date']) ? date('F j, Y', strtotime($data['issued_date'])) : date('F j, Y');
        $pdf->Cell(0, 5, 'Date of Issue: ' . $issuedDate, 0, 1, 'C');
        
        if (!empty($data['verification_code'])) {
            $pdf->Cell(0, 5, 'Verification Code: ' . $data['verification_code'], 0, 1, 'C');
        }
        
        // Signatures
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(60, 60, 60);
        
        // Left signature
        $pdf->SetXY(60, 160);
        $pdf->Cell(60, 8, '______________________', 0, 0, 'C');
        
        // Right signature
        $pdf->SetXY(177, 160);
        $pdf->Cell(60, 8, '______________________', 0, 0, 'C');
        
        // Signature labels
        $pdf->SetXY(60, 168);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 5, 'Head of Department', 0, 0, 'C');
        
        $pdf->SetXY(177, 168);
        $pdf->Cell(60, 5, 'Activity Coordinator', 0, 0, 'C');
        
        return ['success' => true, 'pdf' => $pdf];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'PDF generation failed: ' . $e->getMessage()
        ];
    }
}

// Update database with file path
function updateCertificateFile($cert_id, $relativePath, $connect) {
    $sql = "UPDATE user_certificates SET certificate_file = ?, updated_at = NOW() WHERE certificate_id = ?";
    $stmt = $connect->prepare($sql);
    
    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Database preparation failed: ' . $connect->error
        ];
    }
    
    $stmt->bind_param("si", $relativePath, $cert_id);
    $success = $stmt->execute();
    
    if (!$success) {
        return [
            'success' => false,
            'error' => 'Database update failed: ' . $connect->error
        ];
    }
    
    return ['success' => true];
}

// Main execution
try {
    // Validate input
    $validation = validateAndSanitizeInput();
    if (!$validation['success']) {
        throw new Exception($validation['error']);
    }
    
    $cert_id = $validation['cert_id'];
    
    // Get certificate data
    $certResult = getCertificateData($cert_id, $connect);
    if (!$certResult['success']) {
        throw new Exception($certResult['error']);
    }
    
    $data = $certResult['data'];
    
    // Ensure upload directory exists
    $uploadDir = __DIR__ . '/../uploads/certificates/';
    $dirResult = ensureDirectoryExists($uploadDir);
    if (!$dirResult['success']) {
        throw new Exception($dirResult['error']);
    }
    
    // Find logo
    $logoPath = findLogo();
    
    // Generate PDF
    $pdfResult = generateCertificatePDF($data, $logoPath, $uploadDir);
    if (!$pdfResult['success']) {
        throw new Exception($pdfResult['error']);
    }
    
    $pdf = $pdfResult['pdf'];
    
    // Save PDF file
    $filename = 'cert_' . $cert_id . '_' . date('Ymd_His') . '.pdf';
    $fullSavePath = $uploadDir . $filename;
    $relativePath = 'uploads/certificates/' . $filename;
    
    $pdf->Output('F', $fullSavePath);
    
    // Verify file was created
    if (!file_exists($fullSavePath)) {
        throw new Exception('PDF file was not created successfully');
    }
    
    // Update database
    $updateResult = updateCertificateFile($cert_id, $relativePath, $connect);
    if (!$updateResult['success']) {
        // File was created but DB update failed - clean up
        unlink($fullSavePath);
        throw new Exception($updateResult['error']);
    }
    
    // Success response
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>";
    echo "<h2 style='color: #10b981;'>✅ Certificate Generated Successfully!</h2>";
    echo "<p><strong>Certificate ID:</strong> {$cert_id}</p>";
    echo "<p><strong>Student:</strong> " . htmlspecialchars($data['full_name']) . "</p>";
    echo "<p><strong>Activity:</strong> " . htmlspecialchars($data['activity_name']) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($relativePath) . "</p>";
    echo "<p><strong>File Size:</strong> " . number_format(filesize($fullSavePath) / 1024, 2) . " KB</p>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='../user/view_certificate.php?id={$cert_id}' style='display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View Certificate</a>";
    echo "<a href='../{$relativePath}' target='_blank' style='display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Download PDF</a>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px; padding: 15px; background: #f0f9ff; border-radius: 5px;'>";
    echo "<h4>Next Steps:</h4>";
    echo "<ul>";
    echo "<li>The certificate is now available in the student's certificate dashboard</li>";
    echo "<li>Students can download and view their certificate</li>";
    echo "<li>The verification code can be used to authenticate the certificate</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ef4444; border-radius: 8px; background: #fef2f2;'>";
    echo "<h2 style='color: #ef4444;'>❌ Certificate Generation Failed</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 5px;'>Go Back</a>";
    echo "</div>";
    echo "</div>";
}
?>

<?php
// ===========================
// admin/bulk_generate_certificates.php
// ===========================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Certificate Generation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #374151;
        }
        select, input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .btn {
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #059669;
            transform: translateY(-2px);
        }
        .results {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 Bulk Certificate Generation</h1>
            <p>Generate certificates for multiple participants at once</p>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="activity_id">Select Activity:</label>
                <select name="activity_id" id="activity_id" required>
                    <option value="">Choose an activity...</option>
                    <?php
                    include(__DIR__ . '/../config.php');
                    
                    $activities_sql = "SELECT activity_id, activity_name, activity_date FROM activities ORDER BY activity_date DESC";
                    $activities_result = $connect->query($activities_sql);
                    
                    while ($activity = $activities_result->fetch_assoc()) {
                        echo "<option value='{$activity['activity_id']}'>";
                        echo htmlspecialchars($activity['activity_name']);
                        echo " (" . date('M j, Y', strtotime($activity['activity_date'])) . ")";
                        echo "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="certificate_type">Certificate Type:</label>
                <select name="certificate_type" id="certificate_type" required>
                    <option value="participation">Participation</option>
                    <option value="completion">Completion</option>
                    <option value="achievement">Achievement</option>
                    <option value="excellence">Excellence</option>
                </select>
            </div>

            <div class="form-group">
                <label for="generation_mode">Generation Mode:</label>
                <select name="generation_mode" id="generation_mode" required>
                    <option value="registered">All Registered Participants</option>
                    <option value="completed">Only Completed Participants</option>
                    <option value="missing">Only Missing Certificates</option>
                </select>
            </div>

            <button type="submit" name="generate_bulk" class="btn">🚀 Generate Certificates</button>
        </form>

        <?php
        if (isset($_POST['generate_bulk'])) {
            $activity_id = (int)$_POST['activity_id'];
            $certificate_type = $_POST['certificate_type'];
            $generation_mode = $_POST['generation_mode'];
            
            echo "<div class='results'>";
            echo "<h3>Generation Results:</h3>";
            
            // Build query based on mode
            $conditions = [];
            $joins = ["JOIN users u ON ar.user_id = u.user_id"];
            
            switch ($generation_mode) {
                case 'completed':
                    $conditions[] = "ar.status = 'completed'";
                    break;
                case 'missing':
                    $joins[] = "LEFT JOIN user_certificates uc ON ar.user_id = uc.user_id AND ar.activity_id = uc.activity_id";
                    $conditions[] = "uc.certificate_id IS NULL";
                    break;
            }
            
            $where_clause = $conditions ? "AND " . implode(" AND ", $conditions) : "";
            
            $participants_sql = "
                SELECT DISTINCT ar.user_id, u.user_name, u.matric_number
                FROM activity_registrations ar
                " . implode(" ", $joins) . "
                WHERE ar.activity_id = ? {$where_clause}
                ORDER BY u.user_name
            ";
            
            $stmt = $connect->prepare($participants_sql);
            $stmt->bind_param("i", $activity_id);
            $stmt->execute();
            $participants = $stmt->get_result();
            
            $generated_count = 0;
            $error_count = 0;
            
            while ($participant = $participants->fetch_assoc()) {
                // Generate certificate record
                $verification_code = strtoupper(bin2hex(random_bytes(4)));
                $certificate_name = "Certificate of " . ucfirst($certificate_type);
                
                $insert_sql = "
                    INSERT INTO user_certificates 
                    (user_id, activity_id, certificate_name, certificate_type, verification_code, issued_date, status)
                    VALUES (?, ?, ?, ?, ?, NOW(), 'active')
                ";
                
                $insert_stmt = $connect->prepare($insert_sql);
                $insert_stmt->bind_param("iisss", 
                    $participant['user_id'], 
                    $activity_id, 
                    $certificate_name, 
                    $certificate_type, 
                    $verification_code
                );
                
                if ($insert_stmt->execute()) {
                    $cert_id = $connect->insert_id;

                    // ✅ Automatically generate the certificate PDF
                    $generate = file_get_contents("http://localhost/PCC-project/admin/generate_certificate.php?cert_id=" . $cert_id);

                    echo "<div class='success'>✅ Generated for: " . htmlspecialchars($participant['user_name']) . " (Cert ID: {$cert_id})</div>";
                    $generated_count++;
                    } else {
                    echo "<div class='error'>❌ Failed for: " . htmlspecialchars($participant['user_name']) . "</div>";
                    $error_count++;
                }
            }
            
            echo "<hr>";
            echo "<p><strong>Summary:</strong></p>";
            echo "<p class='success'>Generated: {$generated_count} certificates</p>";
            if ($error_count > 0) {
                echo "<p class='error'>Errors: {$error_count}</p>";
            }
            
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>