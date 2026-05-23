<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT * FROM v_aircraft_maintenance";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Database View Results</h1>
                <p>Showing data from v_aircraft_maintenance VIEW (Requirement #3)</p>
            </div>
        </header>

        <div class="table-card" style="margin-top: 20px;">
            <div class="card-header-flex">
                <h3 class="card-title">Aircraft Maintenance View</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Aircraft ID</th>
                            <th>Model</th>
                            <th>Hangar Name</th>
                            <th>Service Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><span class="badge-table primary"><?php echo htmlspecialchars($row['id_aircraft']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_hangar'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['tgl_servis']); ?></td>
                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='5' style='text-align:center;'>No data available in view.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
