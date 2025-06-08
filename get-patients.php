<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is kader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kader') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$schedule_id = $_GET['schedule_id'] ?? null;

if (!$schedule_id) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID required']);
    exit();
}

try {
    // Get patients for this schedule
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            TIMESTAMPDIFF(YEAR, r.elderly_birth_date, CURDATE()) as age,
            CASE WHEN me.id IS NOT NULL THEN 1 ELSE 0 END as has_examination
        FROM registrations r
        LEFT JOIN medical_examinations me ON r.id = me.registration_id
        WHERE r.schedule_id = ? 
        ORDER BY r.queue_number ASC
    ");
    $stmt->execute([$schedule_id]);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'patients' => $patients
    ]);
    
} catch (PDOException $e) {
    error_log("Error getting patients: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>
