<?php
$host     = "localhost";
$user     = "root";
$password = ""; // Default Laragon itu kosong
$database = "AeroCoreDB";

$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Kalau mau tes, hapus komentar di bawah ini:
// echo "Koneksi Berhasil! Siap terbang, Ka!";
?>