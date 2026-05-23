<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$query = "
SELECT 
    a.id_aircraft, 
    a.model, 
    a.status,
    e.tipe_mesin, 
    e.thrust_power,
    h.nama_hangar, 
    h.lokasi
FROM aircrafts a
JOIN engine_specs e ON a.id_aircraft = e.id_aircraft
LEFT JOIN hangars h ON a.id_hangar = h.id_hangar
ORDER BY a.id_aircraft ASC
";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Results - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Database JOIN Results</h1>
                <p>Showing multi-table JOIN query (Requirement #4)</p>
            </div>
        </header>

        <div class="table-card" style="margin-top: 20px;">
            <div class="card-header-flex">
                <h3 class="card-title">Aircraft Specifications & Hangar Details</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Aircraft ID</th>
                            <th>Model</th>
                            <th>Engine Type</th>
                            <th>Thrust Power</th>
                            <th>Hangar Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'];
                                $badge_class = 'primary';
                                if ($status == 'Grounded' || $status == 'Maintenance') $badge_class = 'danger';
                                elseif ($status == 'Ready') $badge_class = 'primary';
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['id_aircraft']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipe_mesin']); ?></td>
                            <td><?php echo htmlspecialchars($row['thrust_power']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_hangar'] ? ($row['nama_hangar'] . ' (' . $row['lokasi'] . ')') : 'Unassigned'); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='6' style='text-align:center;'>No joined data available.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
