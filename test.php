<?php
echo "PHP berjalan dengan baik!";
echo "<br>";
echo "Waktu sekarang: " . date('Y-m-d H:i:s');

// Test database connection
try {
    require_once 'config/database.php';
    echo "<br>Database connection: OK";
} catch (Exception $e) {
    echo "<br>Database connection error: " . $e->getMessage();
}
?>
