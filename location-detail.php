<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$location_id = $_GET['id'] ?? 1;

// Get location details
try {
    $stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->execute([$location_id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$location) {
        // Default location if not found
        $location = [
            'id' => 1,
            'name' => 'Posyandu Condongcatur',
            'address' => 'Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman, DIY 55283',
            'description' => 'Posyandu Condongcatur merupakan fasilitas kesehatan terpadu yang melayani masyarakat lansia dengan tenaga medis berpengalaman dan fasilitas modern.',
            'image_url' => 'https://via.placeholder.com/600x300'
        ];
    }
    
    // Get schedules for this location
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE location_id = ? AND schedule_date >= CURDATE() ORDER BY schedule_date LIMIT 4");
    $stmt->execute([$location_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no schedules, create default ones
    if (empty($schedules)) {
        $schedules = [
            ['id' => 1, 'schedule_date' => date('Y-m-d', strtotime('+1 day')), 'registered_count' => 12, 'max_capacity' => 50],
            ['id' => 2, 'schedule_date' => date('Y-m-d', strtotime('+8 days')), 'registered_count' => 8, 'max_capacity' => 50],
            ['id' => 3, 'schedule_date' => date('Y-m-d', strtotime('+15 days')), 'registered_count' => 15, 'max_capacity' => 50],
            ['id' => 4, 'schedule_date' => date('Y-m-d', strtotime('+22 days')), 'registered_count' => 5, 'max_capacity' => 50]
        ];
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: locations.php');
    exit();
}

// Sample patients list
$sample_patients = [
    "Siti Aminah (67 tahun)",
    "Budi Santoso (72 tahun)", 
    "Mariam Sari (69 tahun)",
    "Ahmad Wijaya (75 tahun)",
    "Ratna Dewi (68 tahun)"
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($location['name']) ?> - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-4xl mx-auto p-4">
        <div class="mb-6">
            <a href="locations.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Pilihan Lokasi
            </a>
        </div>

        <!-- Location Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="aspect-video bg-gray-200 rounded-t-lg">
                <img src="<?= $location['image_url'] ?? 'https://via.placeholder.com/600x300' ?>" 
                     alt="<?= htmlspecialchars($location['name']) ?>"
                     class="w-full h-full object-cover rounded-t-lg">
            </div>
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($location['name']) ?></h1>
                <p class="text-gray-600 flex items-center mb-4">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?= htmlspecialchars($location['address']) ?>
                </p>
                <p class="text-gray-600"><?= htmlspecialchars($location['description']) ?></p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Schedule -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-calendar mr-2 text-blue-600"></i>
                    Jadwal Posyandu
                </h2>
                <p class="text-gray-600 mb-4">Pilih tanggal untuk mendaftar</p>
                
                <div class="space-y-3">
                    <?php foreach ($schedules as $schedule): ?>
                    <div class="p-3 border rounded-lg cursor-pointer hover:border-gray-300 transition-colors schedule-item" 
                         data-schedule-id="<?= $schedule['id'] ?>"
                         data-date="<?= $schedule['schedule_date'] ?>">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">
                                    <?= date('l, d F Y', strtotime($schedule['schedule_date'])) ?>
                                </p>
                                <p class="text-sm text-gray-600"><?= $schedule['registered_count'] ?> orang terdaftar</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button id="registerBtn" class="w-full mt-4 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors disabled:bg-gray-400" disabled>
                    Pilih Tanggal Terlebih Dahulu
                </button>
            </div>

            <!-- Patient List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-blue-600"></i>
                    Daftar Pasien Terdaftar
                </h2>
                <p class="text-gray-600 mb-4">Pasien yang sudah terdaftar bulan ini</p>
                
                <div class="space-y-2">
                    <?php foreach ($sample_patients as $patient): ?>
                    <div class="p-2 bg-gray-50 rounded">
                        <p class="text-sm"><?= htmlspecialchars($patient) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedScheduleId = null;
        let selectedDate = null;

        document.querySelectorAll('.schedule-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove previous selection
                document.querySelectorAll('.schedule-item').forEach(el => {
                    el.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                // Add selection to clicked item
                this.classList.add('border-blue-500', 'bg-blue-50');
                
                selectedScheduleId = this.dataset.scheduleId;
                selectedDate = this.dataset.date;
                
                // Enable register button
                const registerBtn = document.getElementById('registerBtn');
                registerBtn.disabled = false;
                registerBtn.textContent = 'Daftar untuk Tanggal Ini';
            });
        });

        document.getElementById('registerBtn').addEventListener('click', function() {
            if (selectedScheduleId && selectedDate) {
                window.location.href = `registration.php?schedule_id=${selectedScheduleId}&location_id=<?= $location_id ?>&date=${selectedDate}`;
            }
        });
    </script>
</body>
</html>
