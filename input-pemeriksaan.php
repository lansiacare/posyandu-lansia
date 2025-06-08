<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is kader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kader') {
    header('Location: login.php');
    exit();
}

$registration_id = $_GET['registration_id'] ?? null;

if (!$registration_id) {
    header('Location: kader-dashboard.php');
    exit();
}

// Get registration and patient data
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            ed.blood_type,
            TIMESTAMPDIFF(YEAR, r.elderly_birth_date, CURDATE()) as age,
            s.schedule_date,
            l.name as location_name
        FROM registrations r
        LEFT JOIN elderly_data ed ON r.elderly_nik = ed.nik
        JOIN schedules s ON r.schedule_id = s.id
        JOIN locations l ON s.location_id = l.id
        WHERE r.id = ?
    ");
    $stmt->execute([$registration_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient) {
        header('Location: kader-dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error getting patient data: " . $e->getMessage());
    header('Location: kader-dashboard.php');
    exit();
}

// Get existing examination data if any
$examination = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM medical_examinations WHERE registration_id = ?");
    $stmt->execute([$registration_id]);
    $examination = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error getting examination data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_sugar = !empty($_POST['blood_sugar']) ? floatval($_POST['blood_sugar']) : null;
    $systolic = !empty($_POST['systolic']) ? intval($_POST['systolic']) : null;
    $diastolic = !empty($_POST['diastolic']) ? intval($_POST['diastolic']) : null;
    $cholesterol = !empty($_POST['cholesterol']) ? floatval($_POST['cholesterol']) : null;
    $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : null;
    $height = !empty($_POST['height']) ? floatval($_POST['height']) : null;
    $uric_acid = !empty($_POST['uric_acid']) ? floatval($_POST['uric_acid']) : null;
    $notes = trim($_POST['notes'] ?? '');
    
    try {
        if ($examination) {
            // Update existing examination
            $stmt = $pdo->prepare("
                UPDATE medical_examinations 
                SET blood_sugar = ?, blood_pressure_systolic = ?, blood_pressure_diastolic = ?, 
                    cholesterol = ?, weight = ?, height = ?, uric_acid = ?, notes = ?, 
                    updated_at = NOW()
                WHERE registration_id = ?
            ");
            $stmt->execute([
                $blood_sugar, $systolic, $diastolic, $cholesterol, 
                $weight, $height, $uric_acid, $notes, $registration_id
            ]);
        } else {
            // Insert new examination
            $stmt = $pdo->prepare("
                INSERT INTO medical_examinations 
                (registration_id, kader_id, blood_sugar, blood_pressure_systolic, blood_pressure_diastolic, 
                 cholesterol, weight, height, uric_acid, notes, examination_date, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
            ");
            $stmt->execute([
                $registration_id, $_SESSION['user_id'], $blood_sugar, $systolic, $diastolic,
                $cholesterol, $weight, $height, $uric_acid, $notes
            ]);
        }
        
        header('Location: kader-dashboard.php?success=examination_saved');
        exit();
        
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan saat menyimpan data pemeriksaan.";
        error_log("Error saving examination: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Pemeriksaan - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-4xl mx-auto p-4">
        <div class="mb-6">
            <a href="kader-dashboard.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Patient Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Informasi Pasien
                </h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium"><?= htmlspecialchars($patient['elderly_name']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Umur:</span>
                        <span class="font-medium"><?= $patient['age'] ?> tahun</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Lahir:</span>
                        <span class="font-medium"><?= date('d F Y', strtotime($patient['elderly_birth_date'])) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">NIK:</span>
                        <span class="font-medium"><?= htmlspecialchars($patient['elderly_nik']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">BPJS:</span>
                        <span class="font-medium"><?= htmlspecialchars($patient['elderly_bpjs']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Golongan Darah:</span>
                        <span class="font-medium"><?= $patient['blood_type'] ? htmlspecialchars($patient['blood_type']) : 'Belum diisi' ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Alamat:</span>
                        <p class="font-medium mt-1"><?= htmlspecialchars($patient['elderly_address']) ?></p>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Pemeriksaan:</span>
                        <span class="font-medium"><?= date('d F Y', strtotime($patient['schedule_date'])) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Antrian:</span>
                        <span class="font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded"><?= $patient['queue_number'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Examination Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-stethoscope mr-2 text-green-600"></i>
                    Data Pemeriksaan
                </h2>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-1">
                                Gula Darah (mg/dL)
                            </label>
                            <input type="number" step="0.01" id="blood_sugar" name="blood_sugar"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="80-120"
                                   value="<?= $examination ? htmlspecialchars($examination['blood_sugar']) : '' ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tensi (mmHg)
                            </label>
                            <div class="flex space-x-2">
                                <input type="number" id="systolic" name="systolic"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Sistol"
                                       value="<?= $examination ? htmlspecialchars($examination['blood_pressure_systolic']) : '' ?>">
                                <span class="self-center">/</span>
                                <input type="number" id="diastolic" name="diastolic"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Diastol"
                                       value="<?= $examination ? htmlspecialchars($examination['blood_pressure_diastolic']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="cholesterol" class="block text-sm font-medium text-gray-700 mb-1">
                                Kolesterol (mg/dL)
                            </label>
                            <input type="number" step="0.01" id="cholesterol" name="cholesterol"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="< 200"
                                   value="<?= $examination ? htmlspecialchars($examination['cholesterol']) : '' ?>">
                        </div>
                        
                        <div>
                            <label for="uric_acid" class="block text-sm font-medium text-gray-700 mb-1">
                                Asam Urat (mg/dL)
                            </label>
                            <input type="number" step="0.01" id="uric_acid" name="uric_acid"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="3.5-7.0"
                                   value="<?= $examination ? htmlspecialchars($examination['uric_acid']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                Berat Badan (kg)
                            </label>
                            <input type="number" step="0.1" id="weight" name="weight"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="50.0"
                                   value="<?= $examination ? htmlspecialchars($examination['weight']) : '' ?>">
                        </div>
                        
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-1">
                                Tinggi Badan (cm)
                            </label>
                            <input type="number" step="0.1" id="height" name="height"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="160.0"
                                   value="<?= $examination ? htmlspecialchars($examination['height']) : '' ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Catatan pemeriksaan, keluhan, atau rekomendasi..."><?= $examination ? htmlspecialchars($examination['notes']) : '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <?= $examination ? 'Update' : 'Simpan' ?> Data Pemeriksaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
