<?php
session_start();
include 'includes/db.php'; 


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$alert = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];

  
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmtUser->execute([$_SESSION['username']]);
    $user = $stmtUser->fetch();

    if (!$user) {
        $alert = "error_user"; 
    } else {
        $added_by = $user['id'];

     
        $image = $_FILES['image']['name'];
        $target_dir = "../assets/";
        $target_file = $target_dir . basename($image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        if (!is_numeric($price)) {
            $alert = "error_price";
        } else {
            
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name = ? OR image = ?");
            $stmt->execute([$name, $image]);
            $existing = $stmt->fetch();

            if ($existing) {
                $alert = "error_duplicate";
            } else {
               
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                   
                    $stmt = $pdo->prepare("INSERT INTO products (name, price, image, added_by, date_added) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$name, $price, $image, $added_by]);
                    $alert = "success";
                } else {
                    $alert = "error_upload";
                }
            }
        }
    }
}


$stmt = $pdo->query("SELECT p.*, u.username FROM products p JOIN users u ON p.added_by = u.id ORDER BY date_added DESC");
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISTER BOX Dashboard</title>
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
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body class="light-mode">
    <div class="d-flex" style="min-height:100vh;">
       
        <div class="sidebar">
            <h4>Dashboard</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="index.php" class="nav-link text-white"><i class="bi bi-basket-fill me-2"></i> Order</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="add_menu.php" class="nav-link text-white"><i class="bi bi-card-list me-2"></i> Add Menu</a>
                </li>
                <li class="nav-item mt-4">
                    <a href="login.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
                </li>
            </ul>
        </div>

        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <img src="../assets/logolight.png" alt="Logo" style="height: 50px; margin-right: 10px;">
                <h1>MISTER BOX - Add Menu</h1>
                <button onclick="toggleMode()" class="btn btn-outline-secondary" id="modeBtn">
                    <i class="bi bi-moon-fill"></i> Night
                </button>
            </div>

         
            <div class="card p-3 mb-4">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Price (PHP)</label>
                        <input type="number" name="price" class="form-control" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label>Photo (PNG only)</label>
                        <input type="file" name="image" class="form-control" accept=".png" required>
                    </div>
                    <button type="submit" class="btn btn-black">Add Menu</button>
                </form>
            </div>

    
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Added By</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($items as $item): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?> PHP</td>
                        <td><?= htmlspecialchars($item['username']) ?></td>
                        <td><?= $item['date_added'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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

        <?php if($alert === "success"): ?>
        Swal.fire({ title: "Menu Added!", icon: "success", confirmButtonColor: "#111" });
        <?php elseif($alert === "error_duplicate"): ?>
        Swal.fire({ title: "Error!", text: "Name or photo already exists.", icon: "error", confirmButtonColor: "#111" });
        <?php elseif($alert === "error_price"): ?>
        Swal.fire({ title: "Error!", text: "Price must be a number.", icon: "error", confirmButtonColor: "#111" });
        <?php elseif($alert === "error_upload"): ?>
        Swal.fire({ title: "Error!", text: "Failed to upload image.", icon: "error", confirmButtonColor: "#111" });
        <?php elseif($alert === "error_user"): ?>
        Swal.fire({ title: "Error!", text: "User not found.", icon: "error", confirmButtonColor: "#111" });
        <?php endif; ?>
    </script>
</body>
</html>
