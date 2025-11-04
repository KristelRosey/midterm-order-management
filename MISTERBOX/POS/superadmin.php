<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISTER BOX | Superadmin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        h1 { font-family: 'Cinzel', serif; }
        body.light-mode { background-color: #f8f9fad7; color: #000; }
        body.dark-mode { background-color: #000; color: #e0e0e0; }
        .card {
            transition: transform 0.2s, background-color 0.3s;
            display: flex;
            flex-direction: column;
            max-width: 5000px; 
            margin-bottom: 1rem;
        }
        .card:hover { transform: scale(1.02); box-shadow: 0 0 15px rgba(255, 255, 255, 0.3); }
        .sidebar { width: 220px; flex-shrink: 0; background-color: #111; color: white; min-height: 100vh; padding: 20px; }
        .sidebar h4 { text-align: center; margin-bottom: 30px; }
        .sidebar a { color: white; text-decoration: none; }
        .sidebar a:hover { color: #00bfff; }
        .sidebar .nav-link { padding: 10px 0; }
        .btn-black { background-color: black; border-color: black; color: white; }
        .btn-black:hover { background-color: #333; color: white; }
    </style>
</head>
<body class="light-mode">
    <div class="d-flex" style="min-height:100vh;">
      
        <div class="sidebar">
            <h4>Superadmin</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="register.php" class="nav-link text-white">
                        <i class="bi bi-person-plus-fill me-2"></i> Register Admin
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="employee.php" class="nav-link text-white">
                        <i class="bi bi-people-fill me-2"></i> Manage Employees
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="order.php" class="nav-link text-white">
                        <i class="bi bi-receipt-cutoff me-2"></i> View Orders
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="add_menu_superadmin.php" class="nav-link text-white">
                        <i class="bi bi-plus-circle-fill me-2"></i> Add Menu
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a href="login.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

 
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <img src="../assets/logolight.png" alt="Logo" style="height: 50px; margin-right: 10px;">
                <h1>MISTER BOX - Superadmin Dashboard</h1>
                <button onclick="toggleMode()" class="btn btn-outline-secondary" id="modeBtn">
                    <i class="bi bi-moon-fill"></i> Night
                </button>
            </div>

            <div class="card p-4">
                <h4>Welcome, <span style="color:#00bfff;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h4>
                <p class="mb-0">
                    You have full control over system management. Use the sidebar to:
                </p>
                <ul class="mt-3">
                    <li>Register new <strong>admin accounts</strong>.</li>
                    <li>View or suspend <strong>employee accounts</strong>.</li>
                    <li>Review and export <strong>order transactions</strong>.</li>
                    <li>Add or update <strong>menu items</strong>.</li>
                </ul>
                <p class="mt-3">Choose an option from the left sidebar to get started.</p>
            </div>
        </div>
    </div>

    <script>
        function toggleMode() {
            const body = document.body;
            const btn = document.getElementById('modeBtn');
            const logo = document.querySelector('img');
            if (body.classList.contains('light-mode')) {
                body.classList.replace('light-mode', 'dark-mode');
                btn.innerHTML = '<i class="bi bi-sun-fill"></i> Light';
                logo.src = "../assets/logo.png"; 
            } else {
                body.classList.replace('dark-mode', 'light-mode');
                btn.innerHTML = '<i class="bi bi-moon-fill"></i> Night';
                logo.src = "../assets/logolight.png"; 
            }
        }
    </script>
</body>
</html>
