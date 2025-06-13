<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is kader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kader') {
    header('Location: login.php');
    exit();
}

$kader_id = $_SESSION['user_id'];
$location_id = $_SESSION['location_id'] ?? 1;

// Get kader's location info
try {
    $stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->execute([$location_id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        $location = [
            'id' => 1,
            'name' => 'Posyandu Condongcatur',
            'address' => 'Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman'
        ];
    }
} catch (PDOException $e) {
    $location = [
        'id' => 1,
        'name' => 'Posyandu Condongcatur',
        'address' => 'Jl. Kaliurang KM 7, Condongcatur, Depok, Sleman'
    ];
}

// Get schedules for this location
try {
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE location_id = ? AND schedule_date >= CURDATE() ORDER BY schedule_date LIMIT 10");
    $stmt->execute([$location_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $schedules = [
        ['id' => 1, 'schedule_date' => date('Y-m-d', strtotime('+1 day')), 'registered_count' => 12],
        ['id' => 2, 'schedule_date' => date('Y-m-d', strtotime('+8 days')), 'registered_count' => 8],
        ['id' => 3, 'schedule_date' => date('Y-m-d', strtotime('+15 days')), 'registered_count' => 15]
    ];
}

// Get user info
$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kader - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Kader</h1>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($location['name']) ?></p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-md text-sm text-green-600"></i>
                            </div>
                            <span class="hidden md:block"><?= htmlspecialchars($user['name']) ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profil Kader
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-question-circle mr-2"></i>Bantuan
                            </a>
                            <hr class="my-1">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-4">
        <!-- Location Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($location['name']) ?></h2>
            <p class="text-gray-600 flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>
                <?= htmlspecialchars($location['address']) ?>
            </p>
        </div>

        <!-- Schedules -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                Jadwal Posyandu
            </h3>

            <div class="space-y-4">
                <?php foreach ($schedules as $schedule): ?>
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h4 class="font-medium text-lg">
                                <?= date('l, d F Y', strtotime($schedule['schedule_date'])) ?>
                            </h4>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-users mr-1"></i>
                                <?= $schedule['registered_count'] ?> pasien terdaftar
                            </p>
                        </div>
                        <button class="view-patients-btn bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
                            data-schedule-id="<?= $schedule['id'] ?>"
                            data-date="<?= $schedule['schedule_date'] ?>">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Pasien
                        </button>
                    </div>

                    <div id="patients-<?= $schedule['id'] ?>" class="hidden mt-4 border-t pt-4">
                        <h5 class="font-medium mb-3">Daftar Pasien Terdaftar:</h5>
                        <div class="patients-list space-y-2">
                            <!-- Akan diisi dengan JavaScript -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function() {
            userMenu.classList.add('hidden');
        });

        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // View patients functionality
        document.querySelectorAll('.view-patients-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const scheduleId = this.dataset.scheduleId;
                const patientsDiv = document.getElementById(`patients-${scheduleId}`);
                const patientsList = patientsDiv.querySelector('.patients-list');

                if (patientsDiv.classList.contains('hidden')) {
                    patientsDiv.classList.remove('hidden');
                    this.innerHTML = '<i class="fas fa-eye-slash mr-2"></i>Sembunyikan';

                    fetch(`get-patients.php?schedule_id=${scheduleId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                patientsList.innerHTML = '';
                                data.patients.forEach(patient => {
                                    const patientDiv = document.createElement('div');
                                    patientDiv.className = 'flex justify-between items-center p-3 bg-gray-50 rounded border';
                                    patientDiv.innerHTML = `
                                        <div>
                                            <div class="font-medium">${patient.queue_number}. ${patient.elderly_name}</div>
                                            <div class="text-sm text-gray-600">
                                                ${patient.elderly_nik} | Umur: ${patient.age} tahun
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            ${patient.has_examination ?
                                                '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Sudah Diperiksa</span>' :
                                                '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Belum Diperiksa</span>'
                                            }
                                            <a href="input-pemeriksaan.php?registration_id=${patient.registration_id}"
                                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                                ${patient.has_examination ? 'Edit' : 'Input'} Data
                                            </a>
                                        </div>
                                    `;
                                    patientsList.appendChild(patientDiv);
                                });
                            } else {
                                patientsList.innerHTML = '<p class="text-gray-500 text-center">Tidak ada pasien terdaftar</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            patientsList.innerHTML = '<p class="text-red-500 text-center">Error memuat data pasien</p>';
                        });
                } else {
                    patientsDiv.classList.add('hidden');
                    this.innerHTML = '<i class="fas fa-eye mr-2"></i>Lihat Pasien';
                }
            });
        });
    </script>
</body>
</html>
