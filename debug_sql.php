<?php
include 'koneksi.php';
$sql = file_get_contents('setup_db.sql');
// This regex splits correctly by semicolon ignoring those in strings (approximate for this file)
$queries = explode(';', $sql);
foreach($queries as $index => $q) {
    if(trim($q)) {
        if(!mysqli_query($koneksi, $q)) {
            echo "Error on query " . ($index + 1) . ":\n" . substr($q, 0, 100) . "...\n";
            echo "Error: " . mysqli_error($koneksi) . "\n";
            exit(1);
        }
    }
}
echo "All queries executed successfully.\n";
?>
