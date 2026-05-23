<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// 1. Ambil data pesawat untuk tabel
$query = "SELECT * FROM aircrafts";
$result = mysqli_query($koneksi, $query);

// 2. Query Aggregate: Hitung Total Armada
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM aircrafts");
$data_total = mysqli_fetch_assoc($q_total);
$total_armada = $data_total['total'];

// 3. Query Aggregate: Hitung Total Jam Terbang Seluruh Armada
$q_hours = mysqli_query($koneksi, "SELECT SUM(flight_hours) as total_hours FROM aircrafts");
$data_hours = mysqli_fetch_assoc($q_hours);
$total_hours = $data_hours['total_hours'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroLogix - Fleet Optimization Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons / FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Fleet Optimization Dashboard</h1>
                <p>Real-time logistics, route tracking, and aircraft health.</p>
            </div>
            <div class="header-actions">
                <div class="search-bar">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search tail number, route...">
                </div>
                <button class="icon-btn">
                    <i class="fa-regular fa-bell"></i>
                    <span class="dot"></span>
                </button>
                <button class="btn-secondary" onclick="window.location.href='export.php'"><i class="fa-solid fa-download"></i> Export Data</button>
                <button class="btn-primary" id="syncBtn" onclick="syncSystem()"><i class="fa-solid fa-rotate" id="syncIcon"></i> Sync System</button>
            </div>
        </header>

        <!-- Stat Cards -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Active Fleet</span>
                    <div class="stat-icon icon-blue"><i class="fa-solid fa-plane"></i></div>
                </div>
                <div class="stat-value">
                    <h3><?php echo $total_armada; ?></h3>
                    <span class="stat-trend trend-up"><i class="fa-solid fa-arrow-up"></i> 98%</span>
                </div>
                <span class="stat-desc">In-service aircraft</span>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">AOG Status</span>
                    <div class="stat-icon icon-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div class="stat-value">
                    <h3>3</h3>
                    <span class="stat-trend trend-down"><i class="fa-solid fa-arrow-down"></i> 1 since yest.</span>
                </div>
                <span class="stat-desc">Aircraft on ground</span>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">On-Time Perf.</span>
                    <div class="stat-icon icon-green"><i class="fa-solid fa-clock"></i></div>
                </div>
                <div class="stat-value">
                    <h3>94.2%</h3>
                    <span class="stat-trend trend-up"><i class="fa-solid fa-arrow-up"></i> 2.1%</span>
                </div>
                <span class="stat-desc">Across all logistics hubs</span>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Cargo Volume</span>
                    <div class="stat-icon icon-purple"><i class="fa-solid fa-boxes-stacked"></i></div>
                </div>
                <div class="stat-value">
                    <h3>8.4k Tons</h3>
                    <span class="stat-trend trend-up"><i class="fa-solid fa-arrow-up"></i></span>
                </div>
                <span class="stat-desc">Processed today</span>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="dashboard-grid">
            <!-- Map Card -->
            <div class="map-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Live Fleet Tracking</h3>
                    <div class="map-toggles">
                        <button class="map-toggle-btn">Global</button>
                        <button class="map-toggle-btn active">US Hubs</button>
                    </div>
                </div>
                <div id="fleetMap" class="map-container" style="z-index: 1;">
                    <!-- Leaflet map will render here -->
                </div>
            </div>

            <!-- Routes Card -->
            <div class="routes-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Critical Routes</h3>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="route-list">
                    <!-- Route 1 -->
                    <div class="route-item">
                        <div class="route-header">
                            <span class="flight-num">FLT-8492</span>
                            <span class="badge-status on-time">ON TIME</span>
                        </div>
                        <div class="route-path">
                            <div class="route-point">
                                <h4>LAX</h4>
                                <p>14:30 PST</p>
                            </div>
                            <div class="route-line">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div class="route-point" style="text-align: right;">
                                <h4>JFK</h4>
                                <p>22:45 EST</p>
                            </div>
                        </div>
                        <div class="route-stats">
                            <div class="route-stat">
                                <span>Payload</span>
                                <strong>42t</strong>
                            </div>
                            <div class="route-stat">
                                <span>Fuel Eff.</span>
                                <strong>92%</strong>
                            </div>
                        </div>
                    </div>
                    <!-- Route 2 -->
                    <div class="route-item highlight">
                        <div class="route-header">
                            <span class="flight-num">FLT-1104</span>
                            <span class="badge-status delayed">DELAYED</span>
                        </div>
                        <div class="route-path">
                            <div class="route-point">
                                <h4>ORD</h4>
                                <p>08:15 CST</p>
                            </div>
                            <div class="route-line">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div class="route-point" style="text-align: right;">
                                <h4>LHR</h4>
                                <p>20:30 GMT</p>
                            </div>
                        </div>
                        <button class="route-action-btn" onclick="openRouteModal('FLT-1104')">Re-Route Cargo</button>
                    </div>
                    <!-- Route 3 -->
                    <div class="route-item">
                        <div class="route-header">
                            <span class="flight-num">FLT-7721</span>
                            <span class="badge-status scheduled">SCHEDULED</span>
                        </div>
                        <div class="route-path">
                            <div class="route-point">
                                <h4>MIA</h4>
                                <p>18:00 EST</p>
                            </div>
                            <div class="route-line">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div class="route-point" style="text-align: right;">
                                <h4>GRU</h4>
                                <p>04:30 BRT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="bottom-grid">
            <!-- Chart -->
            <div class="chart-card">
                <div class="card-header-flex" style="margin-bottom: 5px;">
                    <div class="chart-header-info">
                        <h3 class="card-title">Volume vs Capacity</h3>
                        <p>Weekly tonnage processing</p>
                    </div>
                    <select class="select-filter">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>

            <!-- Table -->
            <div class="table-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Maintenance Queue</h3>
                    <i class="fa-solid fa-ellipsis-vertical" style="color: var(--text-secondary); cursor: pointer;"></i>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Aircraft ID</th>
                                <th>Model</th>
                                <th>Flight Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($result && mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $status = $row['status'] ?? 'Unknown';
                                    $badge_class = 'primary'; // default
                                    if ($status == 'Ready') {
                                        $badge_class = 'primary';
                                    } elseif (strpos(strtolower($status), 'maintenance') !== false || strpos(strtolower($status), 'aog') !== false) {
                                        $badge_class = 'danger';
                                    } else {
                                        $badge_class = 'warning';
                                    }
                            ?>
                            <tr>
                                <td><?php echo $row['id_aircraft'] ?? 'N/A'; ?></td>
                                <td><?php echo $row['model'] ?? 'N/A'; ?></td>
                                <td><?php echo number_format($row['flight_hours'] ?? 0); ?> h</td>
                                <td>
                                    <span class="badge-table <?php echo $badge_class; ?>">
                                        <span class="badge-dot"></span>
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center;'>No aircraft data available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Modal Re-Route (Self-Contained Styles to Prevent Cache Issues) -->
    <style>
        .custom-modal-overlay {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s forwards;
        }
        .custom-modal-box {
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 16px;
            width: 420px;
            max-width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transform: translateY(20px);
            animation: slideUp 0.4s forwards;
            position: relative;
        }
        .custom-modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #2B3674;
            margin-bottom: 10px;
        }
        .custom-modal-desc {
            color: #A3AED0;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .custom-select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            font-size: 14px;
            color: #2B3674;
            outline: none;
            margin-bottom: 25px;
            background-color: #F4F7FE;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
        }
        .custom-select:focus {
            border-color: #4318FF;
            background-color: #FFFFFF;
        }
        .custom-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .custom-btn-cancel {
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
            background: transparent;
            color: #2B3674;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .custom-btn-cancel:hover { background: #F4F7FE; }
        .custom-btn-confirm {
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            background: #4318FF;
            color: #FFFFFF;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(67, 24, 255, 0.2);
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .custom-btn-confirm:hover { background: #3311DB; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 24, 255, 0.3); }
    </style>

    <div id="routeModal" class="custom-modal-overlay">
        <div class="custom-modal-box">
            <h3 class="custom-modal-title">Re-Route Cargo <span id="modalFlightNum" style="color:#4318FF;"></span></h3>
            <p class="custom-modal-desc">Select an alternative hub to divert this delayed flight's cargo due to severe weather conditions.</p>
            
            <label style="display:block; font-size:13px; font-weight:600; color:#2B3674; margin-bottom:8px;">Select Alternative Hub</label>
            <select id="routeSelect" class="custom-select">
                <option value="MIA">MIA - Miami International</option>
                <option value="JFK">JFK - John F. Kennedy</option>
                <option value="ATL">ATL - Hartsfield-Jackson</option>
            </select>

            <div class="custom-modal-actions">
                <button class="custom-btn-cancel" onclick="closeRouteModal()">Cancel</button>
                <button class="custom-btn-confirm" id="confirmRouteBtn" onclick="confirmRoute()">Confirm Re-Route</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Fungsi untuk tombol Sync System
        function syncSystem() {
            const btn = document.getElementById('syncBtn');
            const icon = document.getElementById('syncIcon');
            
            // Ubah tampilan tombol menjadi state loading
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
            btn.innerHTML = '<i class="fa-solid fa-rotate fa-spin" id="syncIcon"></i> Syncing...';
            
            // Simulasi proses sinkronisasi dengan delay 1.5 detik
            setTimeout(() => {
                // Refresh halaman untuk memuat data terbaru
                window.location.reload();
            }, 1500);
        }

        // Fungsi Modal Re-Route
        function openRouteModal(flight) {
            document.getElementById('modalFlightNum').innerText = flight;
            document.getElementById('routeModal').style.display = 'flex';
        }
        function closeRouteModal() {
            document.getElementById('routeModal').style.display = 'none';
        }
        function confirmRoute() {
            const btn = document.getElementById('confirmRouteBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            setTimeout(() => {
                alert('Cargo successfully re-routed. New schedule updated.');
                closeRouteModal();
                btn.innerHTML = 'Confirm Re-Route';
                btn.disabled = false;
            }, 1000);
        }

        // Chart.js Initialization
        const ctx = document.getElementById('volumeChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(67, 24, 255, 0.2)');
        gradient.addColorStop(1, 'rgba(67, 24, 255, 0)');

        const volumeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [
                    {
                        label: 'Actual Volume',
                        data: [4000, 4800, 5000, 4900, 5800, 3100, 2900],
                        borderColor: '#4318FF',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#4318FF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Capacity',
                        data: [5000, 5200, 5100, 5500, 6000, 3800, 4000],
                        borderColor: '#A3AED0',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            color: '#A3AED0'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 6000,
                        ticks: {
                            stepSize: 2000,
                            color: '#A3AED0',
                            font: { family: "'Inter', sans-serif", size: 11 }
                        },
                        grid: {
                            color: '#F4F7FE',
                            drawBorder: false,
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#A3AED0',
                            font: { family: "'Inter', sans-serif", size: 11 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });

        // Leaflet Map Initialization
        const map = L.map('fleetMap').setView([38.0, -96.0], 4); // Center over US

        // Add CartoDB Positron tiles for a sleek, modern look
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Define custom HTML markers using existing CSS classes
        const laxJfkHtml = '<div class="map-node leaflet-override"><div class="node-label">LAX-JFK</div><div class="node-dot"></div></div>';
        const dfwMiaHtml = '<div class="map-node leaflet-override"><div class="node-label">DFW-MIA</div><div class="node-dot"></div></div>';
        const aogHtml = '<div class="map-node leaflet-override"><div class="node-label danger">AOG</div><div class="node-dot danger pulse"></div></div>';

        const laxJfkIcon = L.divIcon({className: 'custom-leaflet-icon', html: laxJfkHtml, iconSize: [60, 40], iconAnchor: [30, 20]});
        const dfwMiaIcon = L.divIcon({className: 'custom-leaflet-icon', html: dfwMiaHtml, iconSize: [60, 40], iconAnchor: [30, 20]});
        const aogIcon = L.divIcon({className: 'custom-leaflet-icon', html: aogHtml, iconSize: [40, 40], iconAnchor: [20, 20]});

        // Add markers to the map (approximate coordinates)
        L.marker([36.0, -105.0], {icon: laxJfkIcon}).addTo(map);
        L.marker([32.0, -90.0], {icon: dfwMiaIcon}).addTo(map);
        L.marker([40.0, -80.0], {icon: aogIcon}).addTo(map);
    </script>
</body>
</html>