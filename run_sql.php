<?php
include 'koneksi.php';

$sql = file_get_contents('setup_db.sql');

if (mysqli_multi_query($koneksi, $sql)) {
    do {
        if ($result = mysqli_store_result($koneksi)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($koneksi));
    echo "Database setup successfully executed.\n";
} else {
    echo "Error executing setup: " . mysqli_error($koneksi) . "\n";
}
?>
