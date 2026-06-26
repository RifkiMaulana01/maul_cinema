<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_cinema";
$port = 3307; // Ditambahkan sesuai port di XAMPP Anda

mysqli_report(MYSQLI_REPORT_OFF);

// Kita tambahkan variabel $port di bagian paling akhir perintah hubung
$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("<div style='color:red; font-family:sans-serif; padding:20px;'>
            <strong>Koneksi Gagal:</strong> " . mysqli_connect_error() . "
         </div>");
}
?>
