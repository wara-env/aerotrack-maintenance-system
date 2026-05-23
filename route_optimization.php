<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT * FROM routes";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Optimization - AeroLogix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Route Optimization</h1>
                <p>Manage and optimize flight paths.</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="openMapModal()"><i class="fa-solid fa-map-location-dot"></i> Network Map</button>
            </div>
        </header>

        <div class="table-card" style="margin-top: 20px;">
            <div class="card-header-flex">
                <h3 class="card-title">Scheduled Routes</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Flight Num</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ?? 'Unknown';
                                $badge_class = 'primary';
                                if ($status == 'ON TIME') $badge_class = 'primary';
                                elseif ($status == 'DELAYED') $badge_class = 'danger';
                                elseif ($status == 'SCHEDULED') $badge_class = 'warning';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['origin']); ?></td>
                            <td><?php echo htmlspecialchars($row['destination']); ?></td>
                            <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                            <td><span class="badge-table <?php echo $badge_class; ?>"><span class="badge-dot"></span><?php echo htmlspecialchars($status); ?></span></td>
                        </tr>
                        <?php } } else { echo "<tr><td colspan='6'>No route data</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Network Map Modal -->
    <div id="networkMapModal" class="modal-backdrop" style="display: none;">
        <div class="modal-box" style="width: 800px; max-width: 95%;">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h3>Global <span>Network Map</span></h3>
                <button class="modal-close" type="button" onclick="closeMapModal()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; position: relative;">
                <div id="networkMapContainer" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <script>
        let networkMap = null;

        function openMapModal() {
            const modal = document.getElementById('networkMapModal');
            modal.style.display = 'flex';
            
            // Re-trigger animation
            const box = modal.querySelector('.modal-box');
            box.style.animation = 'none';
            box.offsetHeight;
            box.style.animation = null;

            // Initialize or fix map size
            setTimeout(() => {
                if (!networkMap) {
                    networkMap = L.map('networkMapContainer').setView([20.0, 20.0], 2);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                        subdomains: 'abcd',
                        maxZoom: 20
                    }).addTo(networkMap);

                    // Add markers
                    const laxJfkIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">LAX-JFK</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    const dfwMiaIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">DFW-MIA</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    const ordLhrIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">ORD-LHR</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    const miaGruIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">MIA-GRU</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    
                    // Indonesia routes
                    const cgkDpsIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">CGK-DPS</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    const subUpgIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">SUB-UPG</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    const knoCgkIcon = L.divIcon({className: 'custom-leaflet-icon', html: '<div class="map-node leaflet-override"><div class="node-label">KNO-CGK</div><div class="node-dot"></div></div>', iconSize: [60, 40], iconAnchor: [30, 20]});
                    
                    L.marker([36.0, -105.0], {icon: laxJfkIcon}).addTo(networkMap);
                    L.marker([32.0, -90.0], {icon: dfwMiaIcon}).addTo(networkMap);
                    L.marker([45.0, -60.0], {icon: ordLhrIcon}).addTo(networkMap);
                    L.marker([15.0, -70.0], {icon: miaGruIcon}).addTo(networkMap);
                    
                    // Add Indonesian markers to map
                    L.marker([-7.0, 110.0], {icon: cgkDpsIcon}).addTo(networkMap);
                    L.marker([-5.0, 117.0], {icon: subUpgIcon}).addTo(networkMap);
                    L.marker([1.0, 102.0], {icon: knoCgkIcon}).addTo(networkMap);
                } else {
                    networkMap.invalidateSize();
                }
            }, 300); // Wait for modal animation to finish before rendering Leaflet
        }

        function closeMapModal() {
            document.getElementById('networkMapModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('networkMapModal');
            if (event.target == modal) {
                closeMapModal();
            }
        }
    </script>
</body>
</html>
