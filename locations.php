<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    header('Location: login.php?redirect=locations.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM locations WHERE status = 'active'");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $locations = [];
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Lokasi - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-4xl mx-auto p-4">
        <div class="mb-6">
            <a href="index.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2 mt-4">Pilih Lokasi Posyandu</h1>
            <p class="text-gray-600">Pilih lokasi Posyandu yang paling dekat dengan Anda</p>
        </div>

        <div class="grid gap-6">
            <?php if (empty($locations)): ?>
                <!-- Default locations if database is empty -->
                <?php 
                $default_locations = [
                    [
                        'id' => 1,
                        'name' => 'Posyandu Condongcatur',
                        'address' => 'Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman',
                        'description' => 'Posyandu dengan fasilitas lengkap dan tenaga medis berpengalaman',
                        'total_patients' => 45,
                        'next_schedule' => '2024-01-15'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Posyandu Caturtunggal',
                        'address' => 'Jl. Babarsari, Caturtunggal, Depok, Sleman',
                        'description' => 'Posyandu modern dengan layanan kesehatan terpadu untuk lansia',
                        'total_patients' => 38,
                        'next_schedule' => '2024-01-16'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Posyandu Maguwoharjo',
                        'address' => 'Jl. Raya Maguwoharjo, Maguwoharjo, Depok, Sleman',
                        'description' => 'Posyandu dengan akses mudah dan lingkungan yang nyaman',
                        'total_patients' => 52,
                        'next_schedule' => '2024-01-17'
                    ]
                ];
                $locations = $default_locations;
                ?>
            <?php endif; ?>

            <?php foreach ($locations as $location): ?>
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($location['name']) ?></h3>
                        <p class="text-gray-600 flex items-center mt-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?= htmlspecialchars($location['address']) ?>
                        </p>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4"><?= htmlspecialchars($location['description']) ?></p>

                <div class="flex items-center space-x-6 mb-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-users mr-1"></i>
                        <?= $location['total_patients'] ?? 0 ?> pasien terdaftar
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-1"></i>
                        Jadwal berikutnya: <?= date('d F Y', strtotime($location['next_schedule'] ?? 'today')) ?>
                    </div>
                </div>

                <a href="location-detail.php?id=<?= $location['id'] ?>" 
                   class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                    Lihat Detail & Daftar
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
