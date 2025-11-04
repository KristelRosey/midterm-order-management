<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: login.php');
    exit;
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];


    if ($password !== $confirm) {
        $alert = 'mismatch';
    } else {
 
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $alert = 'exists';
        } else {
     
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, 'admin', 'active')");
            $stmt->execute([$username, $password]);
            $alert = 'success';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MISTER BOX | Register Admin</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #000;
            color: #fff;
        }
        h1 { font-family: 'Cinzel', serif; }

        .sidebar {
            width: 220px;
            flex-shrink: 0;
            background-color: #111;
            color: white;
            min-height: 100vh;
            padding: 20px;
        }
        .sidebar h4 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #00bfff;
        }
        .sidebar .nav-link {
            padding: 10px 0;
        }

        .content {
            flex-grow: 1;
            padding: 40px;
        }

        .register-box {
            background-color: #111;
            padding: 40px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            text-align: center;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }

        .form-control {
            background-color: #222;
            color: white;
            border: none;
            padding: 10px;
        }

        .form-control:focus {
            background-color: #333;
            color: #fff;
            box-shadow: none;
        }

        .btn-black {
            background-color: #000;
            border: 1px solid #fff;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }

        .btn-black:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

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

 
        <div class="content">
            <div class="register-box">
                <img src="../assets/logo.png" alt="MISTER BOX Logo" style="width:80px;margin-bottom:15px;">
                <h1 class="mb-4">Register Admin</h1>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="btn btn-black w-100">Register Admin</button>
                </form>
            </div>
        </div>
    </div>


    <?php if ($alert === 'success'): ?>
    <script>
        Swal.fire({
            title: "Registration Successful",
            text: "Admin account has been created successfully!",
            icon: "success",
            background: "#000",
            color: "#fff",
            confirmButtonColor: "#111"
        });
    </script>
    <?php elseif ($alert === 'exists'): ?>
    <script>
        Swal.fire({
            title: "Error",
            text: "Username already exists! Please choose another.",
            icon: "error",
            background: "#000",
            color: "#fff",
            confirmButtonColor: "#111"
        });
    </script>
    <?php elseif ($alert === 'mismatch'): ?>
    <script>
        Swal.fire({
            title: "Error",
            text: "Passwords do not match!",
            icon: "error",
            background: "#000",
            color: "#fff",
            confirmButtonColor: "#111"
        });
    </script>
    <?php endif; ?>
</body>
</html>
