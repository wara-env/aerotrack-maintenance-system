<?php
include 'koneksi.php';

$sql = "
CREATE OR REPLACE VIEW v_aircraft_maintenance AS
SELECT 
    a.id_aircraft, 
    a.model, 
    h.nama_hangar, 
    m.tgl_servis, 
    m.deskripsi
FROM aircrafts a
LEFT JOIN hangars h ON a.id_hangar = h.id_hangar
JOIN maintenance_logs m ON a.id_aircraft = m.id_aircraft;
";

if (mysqli_query($koneksi, $sql)) {
    echo "View created successfully.\n";
} else {
    echo "Error creating view: " . mysqli_error($koneksi) . "\n";
}
?>
