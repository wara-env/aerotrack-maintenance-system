<?php
include 'koneksi.php';

// Set header HTTP untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=aerotrack_fleet_data_' . date('Ymd_His') . '.csv');

// Buka output stream (langsung ke browser)
$output = fopen('php://output', 'w');

// Query ambil semua data aircrafts
$query = "SELECT * FROM aircrafts";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Ambil baris pertama untuk mendapatkan nama-nama kolom
    $row = mysqli_fetch_assoc($result);
    $columns = array_keys($row);
    
    // Tulis header kolom ke CSV
    fputcsv($output, $columns);
    
    // Tulis baris data pertama
    fputcsv($output, $row);
    
    // Tulis sisa baris data
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
} else {
    // Jika tidak ada data
    fputcsv($output, array('Tidak ada data yang ditemukan.'));
}

fclose($output);
exit();
?>
