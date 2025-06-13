<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/database.php'; // Pastikan path ini benar!

$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get active locations
$locations = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM locations WHERE status = 'active'");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ini akan terpicu jika tabel 'locations' tidak ada atau ada masalah koneksi.
    // Jika Anda melihat pesan error dari sini, itu bagus.
    error_log("Error fetching locations: " . $e->getMessage());
    // Fallback list
    $locations = [
        ['id' => 1, 'name' => 'Posyandu Condongcatur'],
        ['id' => 2, 'name' => 'Posyandu Caturtunggal'],
        ['id' => 3, 'name' => 'Posyandu Maguwoharjo']
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'user';
    $location_id = $_POST['location_id'] ?? null;

    // --- DEBUGGING: Tampilkan input dari form ---
    echo "<pre><h3>DEBUGGING INPUT FORM:</h3>";
    echo "Name: " . htmlspecialchars($name) . "\n";
    echo "Email: " . htmlspecialchars($email) . "\n";
    echo "Password (RAW): " . htmlspecialchars($password) . "\n"; // HATI-HATI di produksi!
    echo "Confirm Password (RAW): " . htmlspecialchars($confirm_password) . "\n";
    echo "User Type: " . htmlspecialchars($user_type) . "\n";
    echo "Location ID: " . ($location_id === null ? 'NULL' : htmlspecialchars($location_id)) . "\n";
    echo "</pre>";
    // --- END DEBUGGING ---

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Silakan isi semua field yang diperlukan.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif ($user_type === 'kader' && empty($location_id)) {
        $error = 'Silakan pilih lokasi posyandu untuk akun kader.';
    } else {
        try {
            // --- DEBUGGING: Cek email yang ada ---
            echo "<pre><h3>DEBUGGING CHECK EXISTING EMAIL:</h3>";
            echo "Checking for email: " . htmlspecialchars($email) . "\n";
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existing_user = $stmt->fetch();
            echo "Existing User Found (if any): ";
            print_r($existing_user);
            echo "</pre>";
            // --- END DEBUGGING ---

            if ($existing_user) {
                $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // NULL-kan location_id jika bukan kader
                if ($user_type !== 'kader') {
                    $location_id = null;
                }

                // --- DEBUGGING: Persiapan INSERT Query ---
                echo "<pre><h3>DEBUGGING INSERT QUERY PREPARATION:</h3>";
                echo "Hashed Password: " . htmlspecialchars($hashed_password) . "\n";
                echo "Final Location ID for INSERT: " . ($location_id === null ? 'NULL' : htmlspecialchars($location_id)) . "\n";
                echo "SQL Query: INSERT INTO users (name, email, password, role, location_id) VALUES (?, ?, ?, ?, ?)\n";
                echo "Parameters to bind:\n";
                echo "1: " . htmlspecialchars($name) . "\n";
                echo "2: " . htmlspecialchars($email) . "\n";
                echo "3: " . "******** (hashed password)\n";
                echo "4: " . htmlspecialchars($user_type) . "\n";
                echo "5: " . ($location_id === null ? 'NULL (PDO::PARAM_NULL)' : htmlspecialchars($location_id) . ' (PDO::PARAM_INT)') . "\n";
                echo "</pre>";
                // --- END DEBUGGING ---

                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, location_id) VALUES (?, ?, ?, ?, ?)");
                
                // Binding parameters
                $stmt->bindParam(1, $name);
                $stmt->bindParam(2, $email);
                $stmt->bindParam(3, $hashed_password);
                $stmt->bindParam(4, $user_type);
                $stmt->bindValue(5, $location_id, is_null($location_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
                
                // --- DEBUGGING: Eksekusi Query ---
                echo "<pre><h3>DEBUGGING EXECUTION:</h3>";
                $execute_success = $stmt->execute();
                echo "Query executed: " . ($execute_success ? 'TRUE' : 'FALSE') . "\n";
                echo "Rows affected: " . $stmt->rowCount() . "\n";
                echo "Last Insert ID: " . $pdo->lastInsertId() . "\n";
                echo "</pre>";
                // --- END DEBUGGING ---


                $user_id = $pdo->lastInsertId();

                if ($execute_success && $stmt->rowCount() > 0) { // Pastikan ada baris yang terpengaruh
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = $user_type;
                    $_SESSION['user_location_id'] = $location_id;

                    // --- DEBUGGING: Redirect ---
                    echo "<h3>Redirecting...</h3>";
                    // --- END DEBUGGING ---

                    if ($user_type === 'kader') {
                        header('Location: kader-dashboard.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit();
                } else {
                    $error = 'Registrasi gagal. Tidak ada data yang tersimpan. Coba periksa log server.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan database: ' . $e->getMessage();
            error_log("Registration DB error: " . $e->getMessage());
            // --- DEBUGGING: Tampilkan pesan error PDO secara langsung ---
            echo "<pre><h3>DATABASE ERROR CAUGHT!</h3>";
            echo "Error message: " . $e->getMessage() . "\n";
            echo "Error code: " . $e->getCode() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
            echo "</pre>";
            // --- END DEBUGGING ---
        }
    }
}

/* // Google simulation - HAPUS ATAU KOMENTARI INI DI PRODUKSI
if (isset($_GET['google'])) {
    $user_type = $_GET['type'] ?? 'user';
    if ($user_type === 'kader') {
        $_SESSION['user_id'] = 4;
        $_SESSION['user_name'] = 'Dr. Google Kader';
        $_SESSION['user_email'] = 'google.kader@example.com';
        $_SESSION['user_role'] = 'kader';
        $_SESSION['user_location_id'] = 1;
        header('Location: kader-dashboard.php');
    } else {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'John Doe';
        $_SESSION['user_email'] = 'john.doe@example.com';
        $_SESSION['user_role'] = 'user';
        $_SESSION['user_location_id'] = null;
        header('Location: index.php');
    }
    exit();
} */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Lansia Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">LC</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Akun Baru</h1>
                <p class="text-gray-600">Buat akun untuk mengakses layanan Posyandu Lansia</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 user-type-option border-blue-500 bg-blue-50" data-type="user">
                            <input type="radio" name="user_type" value="user" class="mr-3" checked>
                            <div>
                                <div class="font-medium">Pengguna Umum</div>
                                <div class="text-xs text-gray-500">Lansia & Keluarga</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 user-type-option" data-type="kader">
                            <input type="radio" name="user_type" value="kader" class="mr-3">
                            <div>
                                <div class="font-medium">Kader Posyandu</div>
                                <div class="text-xs text-gray-500">Petugas Kesehatan</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="nama@email.com">
                </div>
                
                <div id="locationField" class="hidden">
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Posyandu</label>
                    <select id="location_id" name="location_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Lokasi Posyandu</option>
                        <?php foreach ($locations as $location): ?>
                            <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Minimal 6 karakter">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan password yang sama">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Daftar
                </button>
            </form>
            
            <div class="relative flex items-center justify-center my-4">
                <div class="border-t border-gray-300 flex-grow"></div>
                <span class="mx-4 text-sm text-gray-500">atau</span>
                <div class="border-t border-gray-300 flex-grow"></div>
            </div>
            
            <div class="space-y-2">
                <a href="?google=1&type=user" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Daftar sebagai Pengguna Umum
                </a>
                
                <a href="?google=1&type=kader" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Daftar sebagai Kader Posyandu
                </a>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="login.php" class="text-blue-600 hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="text-sm text-gray-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
    
    <script>
        // Handle user type selection
        document.querySelectorAll('.user-type-option').forEach(option => {
            option.addEventListener('click', function() {
                const userType = this.dataset.type;
                const locationField = document.getElementById('locationField');
                const locationSelect = document.getElementById('location_id');
                
                // Remove active class from all options
                document.querySelectorAll('.user-type-option').forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                // Add active class to selected option
                this.classList.add('border-blue-500', 'bg-blue-50');
                
                // Show/hide location field based on user type
                if (userType === 'kader') {
                    locationField.classList.remove('hidden');
                    locationSelect.required = true;
                } else {
                    locationField.classList.add('hidden');
                    locationSelect.required = false;
                    locationSelect.value = '';
                }
            });
        });
        
        // Form validation
        const form = document.querySelector('form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            // Name validation
            if (nameInput.value.length < 3) {
                nameInput.classList.add('border-red-500');
                valid = false;
            } else {
                nameInput.classList.remove('border-red-500');
            }
            
            // Email validation
            if (!emailInput.value.includes('@')) {
                emailInput.classList.add('border-red-500');
                valid = false;
            } else {
                emailInput.classList.remove('border-red-500');
            }
            
            // Password validation
            if (passwordInput.value.length < 6) {
                passwordInput.classList.add('border-red-500');
                valid = false;
            } else {
                passwordInput.classList.remove('border-red-500');
            }
            
            // Confirm password validation
            if (confirmPasswordInput.value !== passwordInput.value) {
                confirmPasswordInput.classList.add('border-red-500');
                valid = false;
            } else {
                confirmPasswordInput.classList.remove('border-red-500');
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
