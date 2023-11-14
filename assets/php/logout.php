<?php
session_start();

// Hapus semua data sesi
session_unset();

// Hentikan sesi
session_destroy();

// Redirect ke halaman login atau halaman utama
header("Location: /ngodingyuk/Login"); // Ganti "login.php" dengan halaman yang sesuai
exit();
?>
