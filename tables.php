<?php
include 'koneksi.php';
$res = mysqli_query($koneksi, 'SHOW TABLES');
while($r = mysqli_fetch_row($res)) {
    echo $r[0] . "\n";
}
?>
