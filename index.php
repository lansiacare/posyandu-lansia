<?php
session_start();
require_once 'config/database.php';

// Get health statistics
try {
    $stmt = $pdo->query("SELECT * FROM health_statistics WHERE is_active = 1 ORDER BY display_order");
    $health_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Default health stats if database is empty
    $health_stats = [
        ['title' => 'Diabetes', 'percentage' => 35, 'description' => 'Lansia dengan diabetes di wilayah Depok'],
        ['title' => 'Hipertensi', 'percentage' => 42, 'description' => 'Lansia dengan tekanan darah tinggi'],
        ['title' => 'Stroke', 'percentage' => 18, 'description' => 'Kasus stroke pada lansia'],
        ['title' => 'Demensia', 'percentage' => 12, 'description' => 'Lansia dengan gangguan kognitif']
    ];
}

// Get health articles
try {
    $stmt = $pdo->query("SELECT * FROM health_articles WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC LIMIT 3");
    $health_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Default articles if database is empty
    $health_articles = [
        [
            'title' => 'Tips Menjaga Kesehatan Jantung di Usia Lanjut',
            'source' => 'Halodoc',
            'url' => 'https://halodoc.com',
            'image_url' => 'https://via.placeholder.com/300x200/EF4444/FFFFFF?text=Kesehatan+Jantung'
        ],
        [
            'title' => 'Pentingnya Olahraga Ringan untuk Lansia',
            'source' => 'Kompas Health',
            'url' => 'https://kompas.com',
            'image_url' => 'https://via.placeholder.com/300x200/059669/FFFFFF?text=Olahraga+Lansia'
        ],
        [
            'title' => 'Nutrisi Seimbang untuk Mencegah Diabetes',
            'source' => 'Detik Health',
            'url' => 'https://detik.com',
            'image_url' => 'https://via.placeholder.com/300x200/F59E0B/FFFFFF?text=Nutrisi+Diabetes'
        ]
    ];
}

// Check if user is logged in and get user role
$is_logged_in = isset($_SESSION['user_id']);
$user = null;
$user_role = 'user';

if ($is_logged_in) {
    try {
        $stmt = $pdo->prepare("SELECT name, email, role, location_id FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user_role = $user['role'];
        }
    } catch (PDOException $e) {
        // Handle error silently
        $is_logged_in = false;
    }
}

// If user is kader, redirect to kader dashboard
if ($is_logged_in && $user_role === 'kader') {
    header('Location: kader-dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lansia Care - Posyandu Lansia Kecamatan Depok</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stat-card {
            transition: all 0.5s ease-in-out;
        }
        
        .article-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <img src="gambar/logo.png" alt="Logo Lansia Care" class="w-12 h-12 rounded-lg object-cover" />                    
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Lansia Care</h1>
                        <p class="text-sm text-gray-600">Kecamatan Depok, Sleman, DIY</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in && $user): ?>
                        <!-- User is logged in -->
                        <div class="relative">
                            <button id="userMenuBtn" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="hidden md:block"><?= htmlspecialchars($user['name']) ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                <a href="account.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Akun Saya
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
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <a href="./login.php" class="text-blue-600 hover:text-blue-800 font-medium">Masuk</a>
                        <a href="./register.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 fade-in">Selamat Datang di Lansia Care</h2>
            <p class="text-xl text-gray-600 mb-8 fade-in">
                Platform terpadu untuk layanan kesehatan lansia di Kecamatan Depok, Sleman
            </p>
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto fade-in">
                <?php if ($is_logged_in && $user): ?>
                    <!-- User is logged in -->
                    <h3 class="text-lg font-semibold mb-2">Halo, <?= htmlspecialchars($user['name']) ?>!</h3>
                    <p class="text-gray-600 mb-4">Pilih lokasi Posyandu untuk memulai</p>
                    <a href="./locations.php" class="inline-flex items-center justify-center w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition-colors text-lg font-medium">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Pilih Lokasi Posyandu
                    </a>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <h3 class="text-lg font-semibold mb-2">Mulai Gunakan Layanan Lansia Care</h3>
                    <p class="text-gray-600 mb-4">Silakan masuk atau daftar untuk mengakses layanan</p>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="./login.php" class="inline-flex items-center justify-center bg-white border border-blue-600 text-blue-600 py-3 px-6 rounded-md hover:bg-blue-50 transition-colors text-lg font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </a>
                        <a href="./register.php" class="inline-flex items-center justify-center bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition-colors text-lg font-medium">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Health Statistics -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h3 class="text-2xl font-bold text-center mb-8">Statistik Kesehatan Lansia</h3>
            <div class="max-w-md mx-auto">
                <div id="statsCard" class="bg-white rounded-lg shadow-lg p-6 stat-card">
                    <div class="text-center">
                        <div id="statPercentage" class="text-4xl font-bold text-blue-600 mb-2">
                            <?= $health_stats[0]['percentage'] ?>%
                        </div>
                        <h4 id="statTitle" class="text-xl font-semibold mb-2">
                            <?= htmlspecialchars($health_stats[0]['title']) ?>
                        </h4>
                        <p id="statDescription" class="text-gray-600 mb-4">
                            <?= htmlspecialchars($health_stats[0]['description']) ?>
                        </p>
                        <div class="flex justify-center space-x-2">
                            <?php for ($i = 0; $i < count($health_stats); $i++): ?>
                            <div class="w-2 h-2 rounded-full stat-indicator <?= $i === 0 ? 'bg-blue-600' : 'bg-gray-300' ?>" data-index="<?= $i ?>"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Health Articles -->
    <section class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h3 class="text-2xl font-bold text-center mb-8">Artikel Kesehatan</h3>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($health_articles as $article): ?>
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow article-card">
                    <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <div class="aspect-video bg-gray-200 rounded-t-lg">
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($article['title']) ?>"
                                 class="w-full h-full object-cover rounded-t-lg">
                        </div>
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-2 text-gray-900 hover:text-blue-600">
                                <?= htmlspecialchars($article['title']) ?>
                            </h4>
                            <p class="text-gray-600 text-sm">
                                Sumber: <?= htmlspecialchars($article['source']) ?>
                            </p>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center mb-4">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold">LC</span>
                </div>
                <span class="text-lg font-semibold">Lansia Care</span>
            </div>
            <p class="text-gray-300 mb-2">Platform terpadu layanan kesehatan lansia</p>
            <p class="text-gray-400">&copy; 2024 Lansia Care - Kecamatan Depok, Sleman, DIY</p>
        </div>
    </footer>

    <script>
        // Health statistics rotation
        const healthStats = <?= json_encode($health_stats) ?>;
        let currentStatIndex = 0;

        function rotateStats() {
            const statPercentage = document.getElementById('statPercentage');
            const statTitle = document.getElementById('statTitle');
            const statDescription = document.getElementById('statDescription');
            const indicators = document.querySelectorAll('.stat-indicator');

            // Update indicators
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('bg-blue-600', index === currentStatIndex);
                indicator.classList.toggle('bg-gray-300', index !== currentStatIndex);
            });

            // Update content with fade effect
            const statsCard = document.getElementById('statsCard');
            statsCard.style.opacity = '0.7';
            
            setTimeout(() => {
                statPercentage.textContent = healthStats[currentStatIndex].percentage + '%';
                statTitle.textContent = healthStats[currentStatIndex].title;
                statDescription.textContent = healthStats[currentStatIndex].description;
                statsCard.style.opacity = '1';
            }, 200);

            currentStatIndex = (currentStatIndex + 1) % healthStats.length;
        }

        // Start rotation
        setInterval(rotateStats, 5000);

        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        if (userMenuBtn) {
            const userMenu = document.getElementById('userMenu');
            
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function() {
                userMenu.classList.add('hidden');
            });

            // Prevent menu from closing when clicking inside
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    </script>
</body>
</html>
