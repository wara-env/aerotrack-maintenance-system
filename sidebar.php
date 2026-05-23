<?php
// sidebar.php
// Get current filename to determine active class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="logo">
        <i class="fa-solid fa-plane-departure"></i>
        AeroLogix
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Overview</div>
        <a href="index.php" class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="fleet_status.php" class="nav-item <?php echo $current_page == 'fleet_status.php' ? 'active' : ''; ?>"><i class="fa-solid fa-plane"></i> Fleet Status</a>
        <a href="cargo_inventory.php" class="nav-item <?php echo $current_page == 'cargo_inventory.php' ? 'active' : ''; ?>"><i class="fa-solid fa-boxes-stacked"></i> Cargo & Inventory</a>
        <a href="route_optimization.php" class="nav-item <?php echo $current_page == 'route_optimization.php' ? 'active' : ''; ?>"><i class="fa-solid fa-route"></i> Route Optimization</a>
        <a href="view_results.php" class="nav-item <?php echo $current_page == 'view_results.php' ? 'active' : ''; ?>"><i class="fa-solid fa-table"></i> DB View Results</a>
        <a href="join_results.php" class="nav-item <?php echo $current_page == 'join_results.php' ? 'active' : ''; ?>"><i class="fa-solid fa-link"></i> DB Join Results</a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Maintenance</div>
        <a href="work_orders.php" class="nav-item <?php echo $current_page == 'work_orders.php' ? 'active' : ''; ?>"><i class="fa-solid fa-wrench"></i> Work Orders</a>
        <a href="inspections.php" class="nav-item <?php echo $current_page == 'inspections.php' ? 'active' : ''; ?>"><i class="fa-solid fa-clipboard-check"></i> Inspections</a>
        <a href="aog_alerts.php" class="nav-item <?php echo $current_page == 'aog_alerts.php' ? 'active' : ''; ?>"><i class="fa-solid fa-triangle-exclamation"></i> AOG Alerts</a>
    </div>

    <div class="user-profile">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'Guest'); ?>&background=4318FF&color=fff" alt="User Avatar" class="user-avatar">
        <div class="user-info">
            <h6><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest'); ?></h6>
            <p><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Role'); ?></p>
        </div>
        <a href="logout.php" style="margin-left:auto; color: #ff5b5b; text-decoration:none;"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
</aside>
