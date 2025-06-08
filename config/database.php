<?php
// Database configuration untuk Posyandu Lansia

// Database credentials
$host = 'localhost';
$dbname = 'posyandu_lansia';
$username = 'root';  // Ganti dengan username database Anda
$password = '';      // Ganti dengan password database Anda

// Untuk hosting, ganti dengan kredensial hosting Anda:
// $host = 'localhost';
// $dbname = 'your_hosting_database_name';
// $username = 'your_hosting_username';
// $password = 'your_hosting_password';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO attributes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Log error untuk debugging
    error_log("Database connection failed: " . $e->getMessage());
    
    // Tampilkan pesan error yang user-friendly
    die("Koneksi database gagal. Silakan coba lagi nanti.");
}

// Helper function untuk escape HTML
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Helper function untuk format tanggal Indonesia
function formatDateIndonesia($date) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $days = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    
    $timestamp = strtotime($date);
    $day = $days[date('l', $timestamp)];
    $date_num = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day, $date_num $month $year";
}

// Helper function untuk generate random queue number
function generateQueueNumber($schedule_id) {
    global $pdo;
    
    // Get existing queue numbers for this schedule
    $stmt = $pdo->prepare("SELECT queue_number FROM registrations WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    $existing_numbers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Generate new unique queue number
    do {
        $queue_number = rand(1, 100);
    } while (in_array($queue_number, $existing_numbers));
    
    return $queue_number;
}
?>
