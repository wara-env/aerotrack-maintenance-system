<?php
include 'koneksi.php';
$tables = ['engine_specs', 'maintenance_logs', 'part_replacements', 'hangars'];
foreach($tables as $t) {
    echo "--- $t ---\n";
    $res = mysqli_query($koneksi, "DESCRIBE $t");
    while($r = mysqli_fetch_assoc($res)) {
        echo "{$r['Field']} - {$r['Type']} - {$r['Key']}\n";
    }
}
?>
