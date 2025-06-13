<?php
session_start();
require_once 'config/database.php';

$error = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on role
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['role'] === 'kader') {
            header('Location: kader-dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } catch (PDOException $e) {
        header('Location: index.php');
        exit();
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'user';
    
    if (empty($email) || empty($password)) {
        $error = 'Silakan isi semua field yang diperlukan.';
    } else {
        try {
            // Check if user exists with specified role
            $stmt = $pdo->prepare("SELECT id, name, email, password, role, location_id FROM users WHERE email = ? AND role = ?");
            $stmt->execute([$email, $user_type]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // For demo purposes, accept any password for existing users
            // In production, use password_verify($password, $user['password'])
            if ($user) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_location_id'] = $user['location_id'];
                
                // Redirect based on role
                if ($user['role'] === 'kader') {
                    header('Location: kader-dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $error = 'Email atau password tidak valid untuk tipe akun yang dipilih.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
/*
// Handle Google login (simulation)
if (isset($_GET['google'])) {
    $user_type = $_GET['type'] ?? 'user';
    
    // Simulate Google login
    if ($user_type === 'kader') {
        $_SESSION['user_id'] = 4; // Demo kader ID
        $_SESSION['user_name'] = 'Dr. Google Kader';
        $_SESSION['user_email'] = 'google.kader@example.com';
        $_SESSION['user_role'] = 'kader';
        $_SESSION['user_location_id'] = 1;
        header('Location: kader-dashboard.php');
    } else {
        $_SESSION['user_id'] = 1; // Demo user ID
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
    <title>Masuk - Lansia Care</title>
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
                <h1 class="text-2xl font-bold text-gray-900">Masuk ke Lansia Care</h1>
                <p class="text-gray-600">Masuk untuk mengakses layanan Posyandu Lansia</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <!-- User Type Selection -->
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
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="nama@email.com">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Masuk
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
                    Masuk sebagai Pengguna Umum
                </a>
                
                <a href="?google=1&type=kader" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk sebagai Kader Posyandu
                </a>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="register.php" class="text-blue-600 hover:underline">Daftar di sini</a>
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
        // Handle user type selection styling
        document.querySelectorAll('.user-type-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.user-type-option').forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                // Add active class to selected option
                this.classList.add('border-blue-500', 'bg-blue-50');
            });
        });
        
        // Form validation
        const form = document.querySelector('form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            // Simple email validation
            if (!emailInput.value.includes('@')) {
                emailInput.classList.add('border-red-500');
                valid = false;
            } else {
                emailInput.classList.remove('border-red-500');
            }
            
            // Password validation
            if (passwordInput.value.length < 1) {
                passwordInput.classList.add('border-red-500');
                valid = false;
            } else {
                passwordInput.classList.remove('border-red-500');
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
