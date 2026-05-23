<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_shipment'])) {
    $tracking_number = $_POST['tracking_number'];
    $description = $_POST['description'];
    $weight_kg = $_POST['weight_kg'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $status = $_POST['status'];

    $insert_query = "INSERT INTO cargo (tracking_number, description, weight_kg, origin, destination, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssdsss", $tracking_number, $description, $weight_kg, $origin, $destination, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Shipment created successfully!";
        } else {
            $error_msg = "Error creating shipment: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Database error.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_shipment'])) {
    $tracking_number = $_POST['tracking_number'];
    $status = $_POST['status'];
    $stmt = mysqli_prepare($koneksi, "UPDATE cargo SET status = ? WHERE tracking_number = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $status, $tracking_number);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Shipment updated successfully!";
        } else {
            $error_msg = "Error updating shipment: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_shipment'])) {
    $tracking_number = $_POST['tracking_number'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM cargo WHERE tracking_number = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $tracking_number);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Shipment deleted successfully!";
        } else {
            $error_msg = "Error deleting shipment: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    }
}

// Generate new Tracking Number for the form
$crg_prefix = "CRG-";
$q_last = "SELECT tracking_number FROM cargo WHERE tracking_number LIKE '$crg_prefix%' ORDER BY id DESC LIMIT 1";
$r_last = mysqli_query($koneksi, $q_last);
$next_num = 1001; // Default starting number if no records exist
if ($r_last && mysqli_num_rows($r_last) > 0) {
    $row_last = mysqli_fetch_assoc($r_last);
    $last_num = (int)substr($row_last['tracking_number'], -4); // Assuming 4 digits like 1001
    $next_num = $last_num + 1;
}
$new_tracking_number = $crg_prefix . str_pad($next_num, 4, "0", STR_PAD_LEFT);

$query = "SELECT * FROM cargo ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo & Inventory - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Cargo & Inventory</h1>
                <p>Track all logistics and shipments.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="openModal('newShipmentModal')"><i class="fa-solid fa-plus"></i> New Shipment</button>
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
                <h3 class="card-title">Active Shipments</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tracking No</th>
                            <th>Description</th>
                            <th>Weight (kg)</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Unknown';
                                $badge_class = 'primary';
                                if ($status == 'Delivered') $badge_class = 'primary';
                                elseif ($status == 'Delayed') $badge_class = 'danger';
                                elseif ($status == 'Scheduled') $badge_class = 'warning';
                                else $badge_class = 'primary';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['tracking_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo number_format($row['weight_kg']); ?></td>
                            <td><?php echo htmlspecialchars($row['origin']) . ' - ' . htmlspecialchars($row['destination']); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($row['tracking_number']); ?>">
                                    <select name="status" style="padding:4px; border-radius:6px; border: 1px solid #E2E8F0; font-family:'Inter', sans-serif;">
                                        <option value="Pending" <?php if($status=='Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Scheduled" <?php if($status=='Scheduled') echo 'selected'; ?>>Scheduled</option>
                                        <option value="In Transit" <?php if($status=='In Transit') echo 'selected'; ?>>In Transit</option>
                                        <option value="Delayed" <?php if($status=='Delayed') echo 'selected'; ?>>Delayed</option>
                                        <option value="Delivered" <?php if($status=='Delivered') echo 'selected'; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" name="update_shipment" style="background:#4318FF; color:white; border:none; padding:5px 10px; border-radius:6px; cursor:pointer;" title="Save Status"><i class="fa-solid fa-save"></i></button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                    <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($row['tracking_number']); ?>">
                                    <button type="submit" name="delete_shipment" style="background:#ff5b5b; color:white; border:none; padding:5px 10px; border-radius:6px; cursor:pointer;" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='6' style='text-align:center;'>No cargo data available.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- New Shipment Modal -->
    <div id="newShipmentModal" class="modal-backdrop" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3>New <span>Shipment</span></h3>
                <button class="modal-close" type="button" onclick="closeModal('newShipmentModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" action="cargo_inventory.php">
                <div class="modal-body">
                    <p>Enter the logistics details to create a new cargo shipment.</p>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="tracking_number">Tracking Number</label>
                        <input type="text" id="tracking_number" name="tracking_number" value="<?php echo htmlspecialchars($new_tracking_number); ?>" readonly style="background-color: #E2E8F0; color: #64748B; cursor: not-allowed; border: none;">
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="description">Cargo Description</label>
                        <input type="text" id="description" name="description" placeholder="E.g. Medical Supplies, Electronics..." required>
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="weight_kg">Weight (kg)</label>
                        <input type="number" step="0.01" id="weight_kg" name="weight_kg" placeholder="E.g. 4500.50" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div class="input-group">
                            <label for="origin">Origin (Airport Code)</label>
                            <input type="text" id="origin" name="origin" placeholder="E.g. LAX" required>
                        </div>
                        <div class="input-group">
                            <label for="destination">Destination (Airport Code)</label>
                            <input type="text" id="destination" name="destination" placeholder="E.g. JFK" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="status">Initial Status</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-spinner select-icon"></i>
                            <select id="status" name="status" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('newShipmentModal')">Cancel</button>
                    <button type="submit" name="create_shipment" class="btn-confirm">Create Shipment</button>
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
            const modal = document.getElementById('newShipmentModal');
            if (event.target == modal) {
                closeModal('newShipmentModal');
            }
        }
    </script>
</body>
</html>
