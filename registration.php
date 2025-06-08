<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$schedule_id = $_GET['schedule_id'] ?? null;
$location_id = $_GET['location_id'] ?? null;
$selected_date = $_GET['date'] ?? null;

if (!$schedule_id || !$location_id || !$selected_date) {
    header('Location: locations.php');
    exit();
}

// Get location info
try {
    $stmt = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
    $stmt->execute([$location_id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$location) {
        $location = ['name' => 'Posyandu Condongcatur'];
    }
} catch (PDOException $e) {
    $location = ['name' => 'Posyandu Condongcatur'];
}

// Get elderly data for current user (for autofill)
$elderly_data = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM elderly_data WHERE user_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $elderly_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error getting elderly data: " . $e->getMessage());
}

// Handle form submission
if ($_POST) {
    try {
        // Generate queue number
        $queue_number = rand(1, 50);
        
        // Insert registration
        $stmt = $pdo->prepare("INSERT INTO registrations (user_id, schedule_id, elderly_name, elderly_nik, elderly_birth_date, elderly_bpjs, elderly_address, queue_number, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $schedule_id,
            $_POST['name'],
            $_POST['nik'],
            $_POST['birthDate'],
            $_POST['bpjsNumber'],
            $_POST['address'],
            $queue_number
        ]);
        
        // Redirect with success message
        header("Location: registration-success.php?queue=$queue_number");
        exit();
        
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        error_log("Registration error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Posyandu - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-2xl mx-auto p-4">
        <div class="mb-6">
            <a href="location-detail.php?id=<?= $location_id ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pendaftaran Posyandu</h1>
            <p class="text-gray-600 mb-6">
                Daftar untuk <?= htmlspecialchars($location['name']) ?> pada tanggal 
                <?= date('l, d F Y', strtotime($selected_date)) ?>
            </p>

            <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <?php if ($elderly_data): ?>
                    <button type="button" id="autofillBtn" class="w-full bg-green-100 text-green-700 py-2 px-4 rounded-md hover:bg-green-200 transition-colors">
                        <i class="fas fa-user-check mr-2"></i>
                        Isi Otomatis dari Data Tersimpan (<?= htmlspecialchars($elderly_data['name']) ?>)
                    </button>
                <?php else: ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Anda belum mengisi data lansia. 
                            <a href="account.php?elderly_form=1" class="underline hover:no-underline">Isi data lansia</a> 
                            terlebih dahulu untuk memudahkan pendaftaran.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan nama lengkap">
                </div>

                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                    <input type="text" id="nik" name="nik" maxlength="16" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan NIK (16 digit)">
                </div>

                <div>
                    <label for="birthDate" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="birthDate" name="birthDate" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="bpjsNumber" class="block text-sm font-medium text-gray-700 mb-1">Nomor BPJS</label>
                    <input type="text" id="bpjsNumber" name="bpjsNumber" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan nomor BPJS">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <input type="text" id="address" name="address" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan alamat lengkap">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Daftar Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
        <?php if ($elderly_data): ?>
        // Real autofill data from database
        const elderlyData = {
            name: <?= json_encode($elderly_data['name']) ?>,
            nik: <?= json_encode($elderly_data['nik']) ?>,
            birthDate: <?= json_encode($elderly_data['birth_date']) ?>,
            bpjsNumber: <?= json_encode($elderly_data['bpjs_number']) ?>,
            address: <?= json_encode($elderly_data['address']) ?>
        };

        document.getElementById('autofillBtn').addEventListener('click', function() {
            // Fill form with real user data
            document.getElementById('name').value = elderlyData.name;
            document.getElementById('nik').value = elderlyData.nik;
            document.getElementById('birthDate').value = elderlyData.birthDate;
            document.getElementById('bpjsNumber').value = elderlyData.bpjsNumber;
            document.getElementById('address').value = elderlyData.address;
            
            // Show success message
            this.innerHTML = '<i class="fas fa-check mr-2"></i>Data berhasil diisi!';
            this.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
            this.classList.add('bg-blue-100', 'text-blue-700');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-user-check mr-2"></i>Isi Otomatis dari Data Tersimpan (<?= htmlspecialchars($elderly_data['name']) ?>)';
                this.classList.remove('bg-blue-100', 'text-blue-700');
                this.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
            }, 2000);
        });
        <?php endif; ?>
    </script>
</body>
</html>
