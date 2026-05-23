<?php
session_start();
include 'koneksi.php';

// Jika sudah login, redirect ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // md5 hash in db

    $hashed_password = md5($password);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$hashed_password'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AeroLogistics</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a; /* Slate 900 */
            --bg-card: #1e293b; /* Slate 800 */
            --bg-input: #0b1120; /* Very dark navy */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --border-color: #334155;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            position: relative;
            z-index: 10;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
        }
        
        .header-logo i {
            color: var(--primary);
            background: rgba(59, 130, 246, 0.15);
            padding: 8px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .header-status {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-divider {
            color: var(--border-color);
            margin: 0 5px;
        }

        .header-links {
            display: flex;
            gap: 20px;
        }

        .header-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .header-links a:hover {
            color: var(--text-main);
        }

        /* Main Content */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Background Grid Lines (Subtle) */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 120px 120px;
            background-position: center center;
            z-index: 0;
            pointer-events: none;
        }
        
        /* 4 decorative dots around card */
        .decorative-dot {
            position: absolute;
            width: 5px;
            height: 5px;
            background-color: var(--primary);
            border-radius: 50%;
            z-index: 1;
            box-shadow: 0 0 12px 2px rgba(59, 130, 246, 0.8);
            opacity: 0.8;
        }
        /* Positions relative to center */
        .dot-tl { top: calc(50% - 180px); left: calc(50% - 320px); }
        .dot-bl { top: calc(50% + 180px); left: calc(50% - 320px); }
        .dot-tr { top: calc(50% - 180px); left: calc(50% + 320px); }
        .dot-br { top: calc(50% + 180px); left: calc(50% + 320px); }

        /* Faint box outline connecting dots */
        .decorative-box {
            position: absolute;
            top: calc(50% - 180px);
            left: calc(50% - 320px);
            right: calc(50% - 320px);
            bottom: calc(50% - 180px);
            border: 1px dashed rgba(255,255,255,0.05);
            z-index: 0;
            pointer-events: none;
            border-radius: 4px;
        }

        /* Login Card */
        .login-card {
            background-color: var(--bg-card);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10;
            position: relative;
            text-align: center;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background-color: var(--bg-input);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: var(--primary);
            font-size: 1.2rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.4);
        }

        .login-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-card .subtitle {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 30px;
        }
        
        .login-card .subtitle a {
            color: var(--text-main);
            font-weight: 600;
            text-decoration: none;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.9rem;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 40px;
            background-color: var(--bg-input);
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--border-color);
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        
        .input-group input::placeholder {
            color: #475569;
        }

        .forgot-link {
            display: block;
            text-align: right;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-decoration: none;
            margin-top: -5px;
            margin-bottom: 25px;
        }
        
        .forgot-link:hover {
            color: var(--text-main);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 25px;
        }

        .login-btn:hover {
            background-color: var(--primary-hover);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider::before {
            margin-right: 1em;
        }
        .divider::after {
            margin-left: 1em;
        }

        .social-login {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            background-color: var(--bg-input);
            border: 1px solid transparent;
            border-radius: 8px;
            color: #cbd5e1;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .social-btn:hover {
            background-color: #0b1120;
            color: var(--text-main);
            border-color: rgba(255,255,255,0.1);
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            font-size: 0.75rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(255,255,255,0.05);
            position: relative;
            z-index: 10;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: var(--text-main);
        }

        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="top-header">
        <div class="header-logo">
            <i class="fa-solid fa-plane-departure"></i>
            AeroLogistics
        </div>
        <div class="header-status">
            <span><span class="status-dot"></span> System Status: Optimal</span>
            <span class="status-divider">|</span>
            <span>Active Flights: 1,248</span>
            <span class="status-divider">|</span>
            <span>Global Hubs: 42 Online</span>
        </div>
        <div class="header-links">
            <a href="#">Support</a>
            <a href="#">Documentation</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Decorative grid and dots -->
        <div class="bg-grid"></div>
        <div class="decorative-box"></div>
        <div class="decorative-dot dot-tl"></div>
        <div class="decorative-dot dot-bl"></div>
        <div class="decorative-dot dot-tr"></div>
        <div class="decorative-dot dot-br"></div>

        <div class="login-card">
            <div class="card-icon">
                <i class="fa-solid fa-plane"></i>
            </div>
            
            <h2>Welcome Back</h2>


            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <i class="fa-regular fa-envelope"></i>
                    <!-- Input username tapi di UI tertulis email address seperti di gambar -->
                    <input type="text" name="username" placeholder="email address" required>
                </div>
                
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <a href="#" class="forgot-link">Forgot password?</a>
                
                <button type="submit" class="login-btn">Login</button>
            </form>


        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-copy">
            &copy; 2026 AeroLogistics Management Systems. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Security</a>
        </div>
    </footer>

</body>
</html>
