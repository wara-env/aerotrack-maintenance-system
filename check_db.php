<?php
include 'koneksi.php';

$q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM aircrafts");
if ($q) {
    $data = mysqli_fetch_assoc($q);
    echo "Total aircrafts: " . $data['total'] . "\n";
} else {
    echo "Error aircrafts: " . mysqli_error($koneksi) . "\n";
}

$q2 = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM cargo_inventory");
if ($q2) {
    $data2 = mysqli_fetch_assoc($q2);
    echo "Total cargo: " . $data2['total'] . "\n";
} else {
    echo "Error cargo: " . mysqli_error($koneksi) . "\n";
}
?>
