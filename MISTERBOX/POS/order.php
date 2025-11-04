<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: login.php');
    exit;
}


$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

$query = "SELECT * FROM orders WHERE 1";
$params = [];


if (!empty($date_start) && !empty($date_end)) {
    $query .= " AND date_added BETWEEN ? AND ?";
    $params = [$date_start . " 00:00:00", $date_end . " 23:59:59"];
} elseif (!empty($date_start)) {
    $query .= " AND date_added >= ?";
    $params = [$date_start . " 00:00:00"];
} elseif (!empty($date_end)) {
    $query .= " AND date_added <= ?";
    $params = [$date_end . " 23:59:59"];
}

$query .= " ORDER BY date_added DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_query = "SELECT SUM(total_amount) AS total_sum FROM orders WHERE 1";
if (!empty($date_start) && !empty($date_end)) {
    $total_query .= " AND date_added BETWEEN ? AND ?";
} elseif (!empty($date_start)) {
    $total_query .= " AND date_added >= ?";
} elseif (!empty($date_end)) {
    $total_query .= " AND date_added <= ?";
}
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute($params);
$total_sum = $total_stmt->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MISTER BOX | View Orders</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        h1 { font-family: 'Cinzel', serif; }
        body.light-mode { background-color: #f8f9fad7; color: #000; }
        body.dark-mode { background-color: #000; color: #e0e0e0; }
        .card { transition: transform 0.2s, background-color 0.3s; margin-bottom: 1rem; }
        .card:hover { transform: scale(1.02); box-shadow: 0 0 15px rgba(255,255,255,0.3); }
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
            <li class="nav-item mb-2"><a href="register.php" class="nav-link text-white"><i class="bi bi-person-plus-fill me-2"></i> Register Admin</a></li>
            <li class="nav-item mb-2"><a href="employee.php" class="nav-link text-white"><i class="bi bi-people-fill me-2"></i> Manage Employees</a></li>
            <li class="nav-item mb-2"><a href="order.php" class="nav-link text-white"><i class="bi bi-receipt-cutoff me-2"></i> View Orders</a></li>
            <li class="nav-item mb-2"><a href="add_menu_superadmin.php" class="nav-link text-white"><i class="bi bi-plus-circle-fill me-2"></i> Add Menu</a></li>
            <li class="nav-item mt-4"><a href="login.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
    </div>

   
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <img src="../assets/logolight.png" alt="Logo" style="height: 50px;">
            <h1>MISTER BOX - Order History</h1>
            <button onclick="toggleMode()" class="btn btn-outline-secondary" id="modeBtn"><i class="bi bi-moon-fill"></i> Night</button>
        </div>

    
        <form method="get" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="date_start" value="<?= htmlspecialchars($date_start) ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="date_end" value="<?= htmlspecialchars($date_end) ?>" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-black me-2"><i class="bi bi-funnel-fill"></i> Filter</button>
                <a href="order.php" class="btn btn-secondary me-2"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                <button type="button" onclick="printReport()" class="btn btn-danger"><i class="bi bi-file-earmark-pdf-fill"></i> Print Report</button>
            </div>
        </form>

   
        <div class="card p-3">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Items</th>
                        <th>Date Added</th>
                        <th>Total (₱)</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($orders): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['user_id']) ?></td>
                            <td><?= htmlspecialchars($order['items']) ?></td>
                            <td><?= htmlspecialchars($order['date_added']) ?></td>
                            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td class="fw-bold">₱<?= number_format($total_sum, 2) ?></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No orders found.</td></tr>
                <?php endif; ?>
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

function printReport() {
    const tableHTML = document.querySelector('.card').outerHTML;
    const dateStart = document.querySelector('[name="date_start"]').value || '---';
    const dateEnd = document.querySelector('[name="date_end"]').value || '---';

    const newWin = window.open('', '', 'width=900,height=700');
    newWin.document.write(`
        <html>
        <head>
            <title>Order Transactions Report</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h2 { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h2>Order Transactions Report</h2>
            <p>Date Range: ${dateStart} to ${dateEnd}</p>
            ${tableHTML}
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() { window.close(); };
                };
            <\/script>
        </body>
        </html>
    `);
    newWin.document.close();
}
</script>
</body>
</html>
