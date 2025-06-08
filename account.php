<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    header('Location: login.php?redirect=account.php');
    exit();
}

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // User not found in database
        session_destroy();
        header('Location: login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $user = [
        'name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? 'user@example.com'
    ];
}

// Check if user has elderly data
$has_elderly_data = false;
$elderly_data = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM elderly_data WHERE user_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $elderly_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $has_elderly_data = $elderly_data ? true : false;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}

// Handle form submission for elderly data
$show_elderly_form = isset($_GET['elderly_form']);

// Handle elderly data form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_elderly_data'])) {
    $name = trim($_POST['name'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $bpjs_number = trim($_POST['bpjs_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $blood_type = $_POST['blood_type'] ?? '';
    
    $error = '';
    
    // Validation
    if (empty($name) || empty($nik) || empty($birth_date) || empty($bpjs_number) || empty($address)) {
        $error = 'Silakan isi semua field yang diperlukan.';
    } elseif (strlen($nik) !== 16 || !ctype_digit($nik)) {
        $error = 'NIK harus terdiri dari 16 digit angka.';
    } else {
        try {
            // Check if NIK already exists for other users
            $stmt = $pdo->prepare("SELECT user_id FROM elderly_data WHERE nik = ? AND user_id != ?");
            $stmt->execute([$nik, $_SESSION['user_id']]);
            $existing_nik = $stmt->fetch();
            
            if ($existing_nik) {
                $error = 'NIK sudah terdaftar untuk pengguna lain.';
            } else {
                if ($has_elderly_data) {
                    // Update existing data
                    $stmt = $pdo->prepare("UPDATE elderly_data SET name = ?, nik = ?, birth_date = ?, blood_type = ?, bpjs_number = ?, address = ?, updated_at = NOW() WHERE user_id = ?");
                    $result = $stmt->execute([$name, $nik, $birth_date, $blood_type, $bpjs_number, $address, $_SESSION['user_id']]);
                } else {
                    // Insert new data
                    $stmt = $pdo->prepare("INSERT INTO elderly_data (user_id, name, nik, birth_date, blood_type, bpjs_number, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $result = $stmt->execute([$_SESSION['user_id'], $name, $nik, $birth_date, $blood_type, $bpjs_number, $address]);
                }
                
                if ($result) {
                    // Redirect to account page with success message
                    header('Location: account.php?success=elderly_data_saved');
                    exit();
                } else {
                    $error = 'Gagal menyimpan data. Silakan coba lagi.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan database. Silakan coba lagi.';
            error_log("Elderly data save error: " . $e->getMessage());
        }
    }
}

$success_message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'elderly_data_saved') {
        $success_message = 'Data lansia berhasil disimpan!';
        // Refresh elderly data after save
        try {
            $stmt = $pdo->prepare("SELECT * FROM elderly_data WHERE user_id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $elderly_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $has_elderly_data = $elderly_data ? true : false;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-2xl mx-auto p-4">
        <div class="mb-6">
            <a href="index.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($show_elderly_form): ?>
            <!-- Elderly Data Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <?= $has_elderly_data ? 'Edit Data Lansia' : 'Isi Data Lansia' ?>
                </h1>
                <p class="text-gray-600 mb-6">Isi data lansia untuk memudahkan pendaftaran Posyandu</p>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan nama lengkap"
                               value="<?= htmlspecialchars($elderly_data['name'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" id="nik" name="nik" maxlength="16" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan NIK (16 digit)"
                               value="<?= htmlspecialchars($elderly_data['nik'] ?? '') ?>">
                        <p class="text-xs text-gray-500 mt-1">NIK harus terdiri dari 16 digit angka</p>
                    </div>
                    
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?= htmlspecialchars($elderly_data['birth_date'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                        <select id="blood_type" name="blood_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Golongan Darah</option>
                            <option value="A" <?= ($elderly_data['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= ($elderly_data['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="AB" <?= ($elderly_data['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                            <option value="O" <?= ($elderly_data['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="bpjs_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor BPJS</label>
                        <input type="text" id="bpjs_number" name="bpjs_number" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan nomor BPJS"
                               value="<?= htmlspecialchars($elderly_data['bpjs_number'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea id="address" name="address" required rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($elderly_data['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" name="save_elderly_data" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            <?= $has_elderly_data ? 'Update Data' : 'Simpan Data' ?>
                        </button>
                        
                        <a href="account.php" class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors text-center">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Account Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-3xl text-gray-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Akun Saya</h1>
                    <p class="text-gray-600">Kelola informasi akun Anda</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-user h-5 w-5 text-gray-600"></i>
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($user['name']) ?></p>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-envelope h-5 w-5 text-gray-600"></i>
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($user['email']) ?></p>
                            <p class="text-sm text-gray-600">Email Terdaftar</p>
                        </div>
                    </div>
                    
                    <?php if ($has_elderly_data && $elderly_data): ?>
                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                        <i class="fas fa-user-check h-5 w-5 text-green-600"></i>
                        <div>
                            <p class="font-medium text-green-800"><?= htmlspecialchars($elderly_data['name']) ?></p>
                            <p class="text-sm text-green-600">Data Lansia Tersimpan</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-3 pt-6">
                    <a href="?elderly_form=1" class="flex items-center w-full bg-white border border-blue-600 text-blue-600 py-2 px-4 rounded-md hover:bg-blue-50 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        <?= $has_elderly_data ? 'Edit Data Lansia' : 'Isi Data Lansia' ?>
                        <?php if ($has_elderly_data): ?>
                            <span class="ml-auto text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Tersimpan</span>
                        <?php endif; ?>
                    </a>

                    <a href="locations.php" class="flex items-center w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Pilih Lokasi Posyandu
                    </a>

                    <a href="hasil-pemeriksaan.php" class="flex items-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-medical mr-2"></i>
                        Lihat Hasil Pemeriksaan
                    </a>

                    <a href="#" class="flex items-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-question-circle mr-2"></i>
                        Bantuan & Dukungan
                    </a>

                    <a href="logout.php" class="flex items-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Keluar
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form validation for elderly data
        const form = document.querySelector('form');
        if (form) {
            const nikInput = document.getElementById('nik');
            
            nikInput.addEventListener('input', function() {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Validate length
                if (this.value.length === 16) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                } else {
                    this.classList.remove('border-green-500');
                    if (this.value.length > 0) {
                        this.classList.add('border-red-500');
                    }
                }
            });
        }
    </script>
</body>
</html>
