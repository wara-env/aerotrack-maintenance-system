<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$success_msg = "";
$error_msg = "";

// Tangani aksi Resolve
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_id'])) {
    $resolve_id = (int)$_POST['resolve_id'];
    $update_query = "UPDATE aog_alerts SET status = 'Resolved' WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $resolve_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: aog_alerts.php");
    exit();
}

// Tangani aksi Report AOG
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_aog'])) {
    $aircraft_id = $_POST['aircraft_id'];
    $location = $_POST['location'];
    $issue_description = $_POST['issue_description'];
    $reported_time = date('Y-m-d H:i:s');
    $status = $_POST['status'];

    $insert_query = "INSERT INTO aog_alerts (aircraft_id, location, issue_description, reported_time, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $aircraft_id, $location, $issue_description, $reported_time, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "AOG Alert reported successfully!";
        } else {
            $error_msg = "Error reporting AOG: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Database error.";
    }
}

$query = "SELECT * FROM aog_alerts ORDER BY reported_time DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AOG Alerts - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>AOG Alerts</h1>
                <p>Critical Aircraft On Ground incidents requiring immediate attention.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" style="background-color: var(--danger-red);" onclick="openModal('reportAogModal')"><i class="fa-solid fa-triangle-exclamation"></i> Report AOG</button>
            </div>
        </header>

        <?php if ($success_msg): ?>
            <div style="background-color: rgba(5, 205, 153, 0.1); color: var(--success-green); padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center;">
                <i class="fa-solid fa-check-circle" style="margin-right: 10px; font-size: 18px;"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div style="background-color: rgba(238, 93, 80, 0.1); color: var(--danger-red); padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 10px; font-size: 18px;"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="table-card" style="margin-top: 20px; border-left: 4px solid var(--danger-red);">
            <div class="card-header-flex">
                <h3 class="card-title">Active Alerts</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Aircraft ID</th>
                            <th>Location</th>
                            <th>Issue Description</th>
                            <th>Reported Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Active';
                                $badge_class = 'danger';
                                if ($status == 'Resolved') $badge_class = 'primary';
                                elseif ($status == 'Active') $badge_class = 'danger';
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['aircraft_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['issue_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['reported_time']); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                            <td>
                                <?php if($status == 'Active'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="resolve_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; background-color: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-color); cursor: pointer;">Resolve</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='6'>No AOG alerts</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Report AOG Modal -->
    <div id="reportAogModal" class="modal-backdrop" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(238, 93, 80, 0.1); color: var(--danger-red);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3>Report <span>AOG Alert</span></h3>
                <button class="modal-close" type="button" onclick="closeModal('reportAogModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" action="aog_alerts.php">
                <div class="modal-body">
                    <p>Report a critical Aircraft On Ground incident requiring immediate attention.</p>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="aircraft_id">Aircraft ID</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-plane select-icon"></i>
                            <select id="aircraft_id" name="aircraft_id" required>
                                <option value="" disabled selected>Select Aircraft</option>
                                <option value="PK-ALX01">PK-ALX01</option>
                                <option value="PK-ALX02">PK-ALX02</option>
                                <option value="PK-ALX03">PK-ALX03</option>
                                <option value="PK-ALX04">PK-ALX04</option>
                                <option value="PK-ALX05">PK-ALX05</option>
                                <option value="PK-ALX06">PK-ALX06</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="location">Location (Airport Code)</label>
                        <input type="text" id="location" name="location" placeholder="E.g. CGK, LAX, SIN" required style="padding-left: 15px;">
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="issue_description">Issue Description</label>
                        <textarea id="issue_description" name="issue_description" placeholder="Describe the technical issue causing the AOG..." required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="status">Initial Status</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-spinner select-icon"></i>
                            <select id="status" name="status" required>
                                <option value="Active" selected>Active</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('reportAogModal')">Cancel</button>
                    <button type="submit" name="report_aog" class="btn-confirm" style="background-color: var(--danger-red); box-shadow: 0 4px 12px rgba(238, 93, 80, 0.2);">Report Alert</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            const box = modal.querySelector('.modal-box');
            box.style.animation = 'none';
            box.offsetHeight;
            box.style.animation = null;
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('reportAogModal');
            if (event.target == modal) {
                closeModal('reportAogModal');
            }
        }
    </script>
</body>
</html>
