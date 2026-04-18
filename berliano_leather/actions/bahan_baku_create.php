<?php
// Mulai session untuk menyimpan pesan feedback
session_start();

// Include file koneksi
include '../config/koneksi.php';

// Cek apakah form disubmit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form dan lakukan sanitasi dasar
    $kode_bahan = mysqli_real_escape_string($koneksi, $_POST['kode_bahan']);
    $nama_bahan = mysqli_real_escape_string($koneksi, $_POST['nama_bahan']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $stok_minimum = (int)$_POST['stok_minimum'];
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga = (float)$_POST['harga'];

    // Buat query INSERT
    $query = "INSERT INTO bahan_baku (kode_bahan, nama_bahan, kategori, stok, stok_minimum, satuan, harga_beli_terakhir)
              VALUES ('$kode_bahan', '$nama_bahan', '$kategori', $stok, $stok_minimum, '$satuan', $harga)";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, set pesan sukses dan redirect
        $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Bahan baku berhasil ditambahkan!'];
    } else {
        // Jika gagal, set pesan error dan redirect
        $_SESSION['feedback'] = ['type' => 'error', 'message' => 'Gagal menambahkan bahan baku: ' . mysqli_error($koneksi)];
    }

    // Redirect kembali ke halaman data bahan
    header("Location: ../admin/data-bahan.php");
    exit();

} else {
    // Jika file diakses langsung, redirect ke halaman utama
    header("Location: ../admin/dashboard.php");
    exit();
}
?>
