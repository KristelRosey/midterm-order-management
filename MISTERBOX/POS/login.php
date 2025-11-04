<?php
session_start();
include 'includes/db.php';

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
  
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $alert = 'success';
        } else {
            $alert = 'error';
        }
    } else {
        $alert = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISTER BOX | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #000;
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }
        .login-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #111;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        h2 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #fff;
        }
        .login-box img {
            width: 80px;
            margin-bottom: 15px;
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
    <div class="login-box">
        <img src="../assets/logo.png" alt="MISTER BOX Logo">
        <h2>MISTER BOX</h2>
        <form method="POST">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" class="btn btn-black w-100">Login</button>
        </form>
    </div>

    <?php if ($alert === 'success'): ?>
    <script>
        Swal.fire({
            title: "Login Successful",
            text: "Welcome back, <?php echo ucfirst($_SESSION['role']); ?>!",
            icon: "success",
            background: "#000",
            color: "#fff",
            confirmButtonColor: "#111",
            confirmButtonText: "Continue"
        }).then(() => {
            <?php if ($_SESSION['role'] === 'superadmin'): ?>
                window.location.href = "superadmin.php";
            <?php else: ?>
                window.location.href = "index.php";
            <?php endif; ?>
        });
    </script>
    <?php elseif ($alert === 'error'): ?>
    <script>
        Swal.fire({
            title: "Login Failed",
            text: "Invalid credentials or suspended account.",
            icon: "error",
            background: "#000",
            color: "#fff",
            confirmButtonColor: "#111",
            confirmButtonText: "Try Again"
        });
    </script>
    <?php endif; ?>
</body>
</html>
