<?php
include 'koneksi.php';
$res = mysqli_query($koneksi, 'DESCRIBE aircrafts');
if ($res) {
    while($r = mysqli_fetch_assoc($res)) {
        print_r($r);
    }
} else {
    echo mysqli_error($koneksi);
}
?>
