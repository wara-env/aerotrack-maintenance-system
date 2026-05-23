<?php
include 'koneksi.php';

$q_hours = mysqli_query($koneksi, "SELECT SUM(flight_hours) as total_hours FROM aircrafts");
if ($q_hours) {
    $data_hours = mysqli_fetch_assoc($q_hours);
    echo "Total hours: " . $data_hours['total_hours'] . "\n";
} else {
    echo "Error hours: " . mysqli_error($koneksi) . "\n";
}
?>
