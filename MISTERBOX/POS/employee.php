<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: login.php');
    exit;
}


if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'suspend') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'unsuspend') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
    }

    header('Location: employee.php');
    exit;
}


$stmt = $pdo->query("SELECT id, username, role, status FROM users ORDER BY role, username");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MISTER BOX | Manage Employees</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-bottom: 1rem;
        }
        .card:hover { transform: scale(1.02); box-shadow: 0 0 15px rgba(255,255,255,0.3); }
        .sidebar { width: 220px; flex-shrink: 0; background-color: #111; color: white; min-height: 100vh; padding: 20px; }
        .sidebar h4 { text-align: center; margin-bottom: 30px; }
        .sidebar a { color: white; text-decoration: none; }
        .sidebar a:hover { color: #00bfff; }
        .sidebar .nav-link { padding: 10px 0; }
        .btn-black { background-color: black; border-color: black; color: white; }
        .btn-black:hover { background-color: #333; color: white; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-suspended { color: #dc3545; font-weight: bold; }
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
            <h1>MISTER BOX - Manage Employees</h1>
            <button onclick="toggleMode()" class="btn btn-outline-secondary" id="modeBtn">
                <i class="bi bi-moon-fill"></i> Night
            </button>
        </div>

       
        <div class="card p-3">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= ucfirst($user['role']) ?></td>
                        <td>
                            <span class="<?= $user['status'] === 'active' ? 'status-active' : 'status-suspended' ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['role'] !== 'superadmin'): ?>
                                <?php if ($user['status'] === 'active'): ?>
                                    <a href="?action=suspend&id=<?= $user['id'] ?>" class="btn btn-sm btn-danger">
                                        <i class="bi bi-person-dash-fill"></i> Suspend
                                    </a>
                                <?php else: ?>
                                    <a href="?action=unsuspend&id=<?= $user['id'] ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-person-check-fill"></i> Unsuspend
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Superadmin</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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

