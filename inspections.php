<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_inspection'])) {
    $aircraft_id = $_POST['aircraft_id'];
    $inspection_type = $_POST['inspection_type'];
    $scheduled_date = $_POST['scheduled_date'];
    $inspector_name = $_POST['inspector_name'];
    $status = $_POST['status'];

    $insert_query = "INSERT INTO inspections (aircraft_id, inspection_type, scheduled_date, inspector_name, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $aircraft_id, $inspection_type, $scheduled_date, $inspector_name, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Inspection scheduled successfully!";
        } else {
            $error_msg = "Error scheduling inspection: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Database error.";
    }
}

$query = "SELECT * FROM inspections ORDER BY scheduled_date ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspections - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Inspections Schedule</h1>
                <p>Routine checks and compliance monitoring.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="openModal('scheduleInspectionModal')"><i class="fa-solid fa-calendar-check"></i> Schedule Inspection</button>
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

        <div class="table-card" style="margin-top: 20px;">
            <div class="card-header-flex">
                <h3 class="card-title">Upcoming Inspections</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Aircraft ID</th>
                            <th>Type</th>
                            <th>Scheduled Date</th>
                            <th>Inspector</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Pending';
                                $badge_class = 'primary';
                                if ($status == 'Completed') $badge_class = 'primary';
                                elseif ($status == 'Pending') $badge_class = 'warning';
                                elseif ($status == 'Scheduled') $badge_class = 'primary';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['aircraft_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['inspection_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['scheduled_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['inspector_name']); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='5'>No inspections scheduled</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Schedule Inspection Modal -->
    <div id="scheduleInspectionModal" class="modal-backdrop" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h3>Schedule <span>Inspection</span></h3>
                <button class="modal-close" type="button" onclick="closeModal('scheduleInspectionModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" action="inspections.php">
                <div class="modal-body">
                    <p>Fill in the details below to schedule a new aircraft inspection.</p>

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
                        <label for="inspection_type">Inspection Type</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-list-check select-icon"></i>
                            <select id="inspection_type" name="inspection_type" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="A-Check">A-Check</option>
                                <option value="B-Check">B-Check</option>
                                <option value="C-Check">C-Check</option>
                                <option value="D-Check">D-Check</option>
                                <option value="Transit Check">Transit Check</option>
                                <option value="Daily Check">Daily Check</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="scheduled_date">Scheduled Date</label>
                        <input type="date" id="scheduled_date" name="scheduled_date" required style="padding-left: 15px;">
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="inspector_name">Inspector Name</label>
                        <input type="text" id="inspector_name" name="inspector_name" placeholder="E.g. Eng. Sarah Jenkins" required style="padding-left: 15px;">
                    </div>

                    <div class="input-group">
                        <label for="status">Initial Status</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-spinner select-icon"></i>
                            <select id="status" name="status" required>
                                <option value="Scheduled" selected>Scheduled</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('scheduleInspectionModal')">Cancel</button>
                    <button type="submit" name="schedule_inspection" class="btn-confirm">Schedule</button>
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
            const modal = document.getElementById('scheduleInspectionModal');
            if (event.target == modal) {
                closeModal('scheduleInspectionModal');
            }
        }
    </script>
</body>
</html>
