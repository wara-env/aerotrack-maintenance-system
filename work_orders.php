<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_order'])) {
    $order_number = $_POST['order_number'];
    $aircraft_id = $_POST['aircraft_id'];
    $task_description = $_POST['task_description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    $insert_query = "INSERT INTO work_orders (order_number, aircraft_id, task_description, priority, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $order_number, $aircraft_id, $task_description, $priority, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Work order created successfully!";
        } else {
            $error_msg = "Error creating work order: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Database error.";
    }
}

// Generate new Order Number for the form
$wo_prefix = "WO-" . date("Y") . "-";
$q_last = "SELECT order_number FROM work_orders WHERE order_number LIKE '$wo_prefix%' ORDER BY id DESC LIMIT 1";
$r_last = mysqli_query($koneksi, $q_last);
$next_num = 1;
if ($r_last && mysqli_num_rows($r_last) > 0) {
    $row_last = mysqli_fetch_assoc($r_last);
    $last_num = (int)substr($row_last['order_number'], -3);
    $next_num = $last_num + 1;
}
$new_order_number = $wo_prefix . str_pad($next_num, 3, "0", STR_PAD_LEFT);

$query = "SELECT * FROM work_orders ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Work Orders</h1>
                <p>Manage maintenance tasks and team assignments.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="openModal('createOrderModal')"><i class="fa-solid fa-plus"></i> Create Order</button>
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
                <h3 class="card-title">Active Work Orders</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Aircraft ID</th>
                            <th>Task Description</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Open';
                                $badge_class = 'primary';
                                if ($status == 'Completed') $badge_class = 'primary';
                                elseif ($status == 'Open') $badge_class = 'warning';
                                elseif ($status == 'In Progress') $badge_class = 'primary';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['aircraft_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['task_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['priority']); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='5'>No work orders found</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create Order Modal -->
    <div id="createOrderModal" class="modal-backdrop" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <h3>Create <span>Work Order</span></h3>
                <button class="modal-close" type="button" onclick="closeModal('createOrderModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" action="work_orders.php">
                <div class="modal-body">
                    <p>Fill in the details below to create a new maintenance work order.</p>
                    
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="order_number">Order Number</label>
                        <input type="text" id="order_number" name="order_number" value="<?php echo htmlspecialchars($new_order_number); ?>" readonly style="background-color: #E2E8F0; color: #64748B; cursor: not-allowed; border: none;">
                    </div>

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
                        <label for="task_description">Task Description</label>
                        <textarea id="task_description" name="task_description" placeholder="Describe the maintenance task..." required></textarea>
                    </div>

                    <div class="input-group" style="margin-bottom: 15px;">
                        <label for="priority">Priority</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-flag select-icon"></i>
                            <select id="priority" name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="status">Initial Status</label>
                        <div class="select-wrapper">
                            <i class="fa-solid fa-spinner select-icon"></i>
                            <select id="status" name="status" required>
                                <option value="Open" selected>Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('createOrderModal')">Cancel</button>
                    <button type="submit" name="create_order" class="btn-confirm">Create Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            // Reset animation
            const box = modal.querySelector('.modal-box');
            box.style.animation = 'none';
            box.offsetHeight; // trigger reflow
            box.style.animation = null;
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'none';
        }
        
        // Close when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('createOrderModal');
            if (event.target == modal) {
                closeModal('createOrderModal');
            }
        }
    </script>
</body>
</html>
