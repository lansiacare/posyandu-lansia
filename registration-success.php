<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$queue_number = $_GET['queue'] ?? null;

if (!$queue_number) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-2xl mx-auto p-4 flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pendaftaran Berhasil!</h1>
            <p class="text-gray-600 mb-6">Terima kasih telah mendaftar di Posyandu Lansia</p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600 mb-1">Nomor Antrian Anda:</p>
                <p class="text-3xl font-bold text-blue-600"><?= htmlspecialchars($queue_number) ?></p>
            </div>
            
            <p class="text-sm text-gray-600 mb-6">
                Simpan nomor antrian ini dan datang sesuai jadwal yang telah dipilih. 
                Anda akan menerima reminder melalui email.
            </p>
            
            <div class="space-y-3">
                <a href="index.php" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block">
                    Kembali ke Beranda
                </a>
                <a href="locations.php" class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors inline-block">
                    Daftar Lokasi Lain
                </a>
            </div>
        </div>
    </div>
</body>
</html>
