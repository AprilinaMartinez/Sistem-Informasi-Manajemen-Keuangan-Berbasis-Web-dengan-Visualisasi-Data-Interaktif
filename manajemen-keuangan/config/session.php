<?php
/**
 * Session Configuration & Authentication Check
 */

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Fungsi untuk redirect ke login jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /manajemen-keuangan/login.php');
        exit;
    }
}

// Fungsi untuk mendapatkan user ID yang sedang login
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Fungsi untuk mendapatkan username yang sedang login
function getUsername() {
    return $_SESSION['username'] ?? 'Guest';
}

// Fungsi untuk mendapatkan nama lengkap yang sedang login
function getNamaLengkap() {
    return $_SESSION['nama_lengkap'] ?? 'User';
}

// Fungsi untuk mendapatkan foto profil
function getFotoProfil() {
    return $_SESSION['foto_profil'] ?? 'default-avatar.png';
}

// Fungsi untuk logout
function logout() {
    session_destroy();
    header('Location: /manajemen-keuangan/login.php');
    exit;
}
