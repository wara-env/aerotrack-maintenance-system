<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT * FROM aircrafts";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Status - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Fleet Status</h1>
                <p>Complete overview of all aircraft in the network.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="window.location.href='export.php'"><i class="fa-solid fa-download"></i> Export Fleet</button>
            </div>
        </header>

        <div class="table-card" style="margin-top: 20px;">
            <div class="card-header-flex">
                <h3 class="card-title">All Aircraft</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Flight Hours</th>
                            <th>Last Maintenance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Unknown';
                                $badge_class = 'primary';
                                if ($status == 'Ready') $badge_class = 'primary';
                                elseif (strpos(strtolower($status), 'maintenance') !== false) $badge_class = 'danger';
                                else $badge_class = 'warning';
                        ?>
                        <tr>
                            <td><?php echo $row['id_aircraft']; ?></td>
                            <td><?php echo $row['model']; ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                            <td><?php echo number_format($row['flight_hours']); ?> h</td>
                            <td><?php echo $row['last_maintenance'] ?? 'N/A'; ?></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='5'>No data</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
