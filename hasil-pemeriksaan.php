<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's examination results
try {
    $stmt = $pdo->prepare("
        SELECT 
            me.*,
            r.elderly_name,
            r.queue_number,
            s.schedule_date,
            l.name as location_name,
            u.name as kader_name
        FROM medical_examinations me
        JOIN registrations r ON me.registration_id = r.id
        JOIN schedules s ON r.schedule_id = s.id
        JOIN locations l ON s.location_id = l.id
        JOIN users u ON me.kader_id = u.id
        WHERE r.user_id = ?
        ORDER BY me.examination_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $examinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error getting examination results: " . $e->getMessage());
    $examinations = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-4xl mx-auto p-4">
        <div class="mb-6">
            <a href="account.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Akun
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-file-medical mr-3 text-blue-600"></i>
                Hasil Pemeriksaan Kesehatan
            </h1>

            <?php if (empty($examinations)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-file-medical text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Hasil Pemeriksaan</h3>
                    <p class="text-gray-600 mb-4">Anda belum memiliki hasil pemeriksaan kesehatan.</p>
                    <a href="locations.php" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Daftar Pemeriksaan
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($examinations as $exam): ?>
                    <div class="border rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?= htmlspecialchars($exam['location_name']) ?>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Tanggal: <?= date('d F Y', strtotime($exam['schedule_date'])) ?> | 
                                    Antrian: <?= $exam['queue_number'] ?> |
                                    Pemeriksa: <?= htmlspecialchars($exam['kader_name']) ?>
                                </p>
                            </div>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                Selesai
                            </span>
                        </div>

                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if ($exam['blood_sugar']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Gula Darah</div>
                                <div class="text-lg font-semibold">
                                    <?= number_format($exam['blood_sugar'], 1) ?> mg/dL
                                </div>
                                <div class="text-xs <?= $exam['blood_sugar'] > 140 ? 'text-red-600' : ($exam['blood_sugar'] < 70 ? 'text-yellow-600' : 'text-green-600') ?>">
                                    <?= $exam['blood_sugar'] > 140 ? 'Tinggi' : ($exam['blood_sugar'] < 70 ? 'Rendah' : 'Normal') ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exam['blood_pressure_systolic'] && $exam['blood_pressure_diastolic']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Tekanan Darah</div>
                                <div class="text-lg font-semibold">
                                    <?= $exam['blood_pressure_systolic'] ?>/<?= $exam['blood_pressure_diastolic'] ?> mmHg
                                </div>
                                <div class="text-xs <?= $exam['blood_pressure_systolic'] > 140 || $exam['blood_pressure_diastolic'] > 90 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= $exam['blood_pressure_systolic'] > 140 || $exam['blood_pressure_diastolic'] > 90 ? 'Tinggi' : 'Normal' ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exam['cholesterol']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Kolesterol</div>
                                <div class="text-lg font-semibold">
                                    <?= number_format($exam['cholesterol'], 1) ?> mg/dL
                                </div>
                                <div class="text-xs <?= $exam['cholesterol'] > 200 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= $exam['cholesterol'] > 200 ? 'Tinggi' : 'Normal' ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exam['uric_acid']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Asam Urat</div>
                                <div class="text-lg font-semibold">
                                    <?= number_format($exam['uric_acid'], 1) ?> mg/dL
                                </div>
                                <div class="text-xs <?= $exam['uric_acid'] > 7 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= $exam['uric_acid'] > 7 ? 'Tinggi' : 'Normal' ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exam['weight']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Berat Badan</div>
                                <div class="text-lg font-semibold">
                                    <?= number_format($exam['weight'], 1) ?> kg
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($exam['height']): ?>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-sm text-gray-600">Tinggi Badan</div>
                                <div class="text-lg font-semibold">
                                    <?= number_format($exam['height'], 1) ?> cm
                                </div>
                                <?php if ($exam['weight'] && $exam['height']): ?>
                                    <?php 
                                    $bmi = $exam['weight'] / (($exam['height'] / 100) ** 2);
                                    $bmi_status = $bmi < 18.5 ? 'Kurang' : ($bmi > 25 ? 'Berlebih' : 'Normal');
                                    $bmi_color = $bmi < 18.5 ? 'text-yellow-600' : ($bmi > 25 ? 'text-red-600' : 'text-green-600');
                                    ?>
                                    <div class="text-xs <?= $bmi_color ?>">
                                        BMI: <?= number_format($bmi, 1) ?> (<?= $bmi_status ?>)
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($exam['notes']): ?>
                        <div class="mt-4 p-3 bg-blue-50 rounded">
                            <div class="text-sm font-medium text-blue-900 mb-1">Catatan Kader:</div>
                            <div class="text-sm text-blue-800"><?= htmlspecialchars($exam['notes']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
