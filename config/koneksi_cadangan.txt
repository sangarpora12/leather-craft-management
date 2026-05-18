<?php
// Parameter Koneksi Database
$DB_HOST = 'localhost'; // atau 127.0.0.1
$DB_USER = 'root';      // username database
$DB_PASS = '';          // password database
$DB_NAME = 'db_berliano'; // nama database

// Membuat Koneksi
$koneksi = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Cek Koneksi
if (!$koneksi) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Set timezone default
date_default_timezone_set('Asia/Jakarta');
?>
