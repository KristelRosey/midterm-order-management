<?php

session_start();


if (file_exists('includes/db.php')) {
    require_once 'includes/db.php';
} else {
    die("Error: Database connection file not found at 'includes/db.php'");
}


if (!isset($pdo)) {
    die("Error: Database connection failed. Please check your db.php file.");
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_order'])) {
    try {
        $orderData = json_decode($_POST['order_data'], true);
        $totalAmount = floatval($_POST['total_amount']);
        
       
        if (empty($orderData) || $totalAmount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order data']);
            exit;
        }
        
    
        $itemsJson = json_encode($orderData);
        
   
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, items, total_amount, date_added) VALUES (?, ?, ?, NOW())");
        $success = $stmt->execute([$userId, $itemsJson, $totalAmount]);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Order saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save order']);
        }
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving order: ' . $e->getMessage()]);
        exit;
    }
}

try {
    $query = "SELECT id, name, price, image FROM products ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching products: " . $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISTER BOX</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        h1 {
            font-family: 'Cinzel', serif;
        }
        body.light-mode {
            background-color: #f8f9fad7;
            color: #000000;
        }
        body.dark-mode {
            background-color: #000000;
            color: #e0e0e0;
        }
        .card {
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }
        .modal-content {
            transition: background-color 0.3s, color 0.3s;
        }
        body.dark-mode .modal-content {
            background-color: #000000;
            color: #e0e0e0;
        }
        .modal-backdrop.show {
            backdrop-filter: blur(5px);
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .btn-black {
            background-color: black;
            border-color: black;
            color: white;
        }
        .btn-black:hover {
            background-color: #333333;
            border-color: #333333;
            color: white;
        }
        .btn-gray {
            background-color: #333333;
            border-color: #333333;
            color: white;
        }
        .btn-gray:hover {
            background-color: darkred;
            border-color: darkred;
            color: white;
        }
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
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
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
                <h1>MISTER BOX</h1>
                <button onclick="toggleMode()" class="btn btn-outline-secondary" id="modeBtn">
                    <i class="bi bi-moon-fill"></i> Night
                </button>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h3>Menu</h3>
                    <div id="menu">
                        <?php
                        
                        if (isset($error_message)) {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
                        }
                        
                    
                        $counter = 0;
                        foreach ($products as $index => $product) {
                           
                            if ($counter % 4 == 0) {
                                if ($counter > 0) echo '</div>'; 
                                echo '<div class="row mb-4">';
                            }
                            ?>
                            <div class="col-md-3 d-flex">
                                <div class="card w-100 p-2 d-flex flex-column justify-content-between">
                                    <img src="../assets/<?php echo htmlspecialchars($product['image']); ?>" 
                                         class="card-img-top mb-2" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.src='../assets/placeholder.png'">
                                    <div class="card-body d-flex flex-column justify-content-between text-center">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p><?php echo number_format($product['price'], 2); ?> PHP</p>
                                        <input type="number" 
                                               id="qty<?php echo $index; ?>" 
                                               class="form-control mb-2" 
                                               min="0" 
                                               max="99" 
                                               value="0" 
                                               oninput="validateQty(this)">
                                        <button class="btn btn-black" onclick="addToOrder(<?php echo $index; ?>)">
                                            Add to Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $counter++;
                        }
                     
                        if ($counter > 0) echo '</div>';
                        
                       
                        if (empty($products) && !isset($error_message)) {
                            echo '<div class="alert alert-info">No products available. Please add items from the Add Menu page.</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <h3>Ordered Items</h3>
                    <ul class="list-group mb-3" id="orderList"></ul>
                    <h5>Total: <span id="total">0</span> PHP</h5>
                    <input type="number" class="form-control my-2" id="moneyInput" placeholder="Enter payment amount" min="0">
                    <button class="btn btn-success w-100 mb-2" onclick="processPayment()">Pay</button>
                    <button class="btn btn-gray w-100" onclick="confirmCancel()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel">Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="clearOrder()"></button>
                </div>
                <div class="modal-body" id="receiptContent"></div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="printReceipt()">Print</button>
                    <button type="button" class="btn btn-success" onclick="saveOrder()" id="doneBtn">Done</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const items = <?php echo json_encode($products); ?>;
        const order = [];

        function validateQty(input) {
            let value = parseInt(input.value) || 0;
            input.value = Math.max(0, Math.min(99, value));
        }

        function addToOrder(index) {
            const qty = parseInt(document.getElementById(`qty${index}`).value);
            if (qty > 0) {
                const item = items[index];
                const existing = order.find(o => o.name === item.name);
                if (existing) {
                    existing.quantity += qty;
                } else {
                    order.push({ 
                        name: item.name, 
                        price: parseFloat(item.price), 
                        quantity: qty 
                    });
                }
                updateOrderList();
                document.getElementById(`qty${index}`).value = 0;
            }
        }

        function updateOrderList() {
            const list = document.getElementById('orderList');  
            list.innerHTML = '';
            let total = 0;
            order.forEach(item => {
                total += item.price * item.quantity;
                list.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${item.name} x${item.quantity} 
                        <span class="badge bg-black">${(item.price * item.quantity).toFixed(2)} PHP</span>
                    </li>`;
            });
            document.getElementById('total').innerText = total.toFixed(2);
        }

        function processPayment() {
            const total = parseFloat(document.getElementById('total').innerText);
            const money = parseFloat(document.getElementById('moneyInput').value);

            if (total === 0) {
                alert('No items ordered.');
                return;
            }
            if (isNaN(money) || money < total) {
                alert('Not enough balance. Please try again.');
                return;
            }

            let receipt = '<strong>---- RECEIPT ----</strong><br>';
            order.forEach(item => {
                receipt += `${item.name} x ${item.quantity} = ${(item.price * item.quantity).toFixed(2)} PHP<br>`;
            });
            receipt += `----------------------<br>`;
            receipt += `<strong>Total:</strong> ${total.toFixed(2)} PHP<br>`;
            receipt += `<strong>Money Received:</strong> ${money.toFixed(2)} PHP<br>`;
            receipt += `<strong>Change:</strong> ${(money - total).toFixed(2)} PHP`;

            document.getElementById('receiptContent').innerHTML = receipt;
            new bootstrap.Modal(document.getElementById('receiptModal')).show();

            items.forEach((_, index) => {
                const qtyInput = document.getElementById(`qty${index}`);
                if (qtyInput) qtyInput.value = 0;
            });
        }

        function printReceipt() {
            const printContent = document.getElementById('receiptContent').innerHTML;
            const win = window.open('', '', 'height=600,width=400');
            win.document.write('<html><head><title>Receipt</title></head><body>');
            win.document.write(printContent);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }

        function saveOrder() {
            const total = parseFloat(document.getElementById('total').innerText);
            const doneBtn = document.getElementById('doneBtn');
            
            if (order.length === 0) {
                alert('No items to save.');
                return;
            }

         
            doneBtn.disabled = true;
            doneBtn.innerHTML = 'Saving...';

    
            const formData = new FormData();
            formData.append('save_order', '1');
            formData.append('order_data', JSON.stringify(order));
            formData.append('total_amount', total);

          
            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order saved successfully!');
                  
                    const modalElement = document.getElementById('receiptModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    clearOrder();
                } else {
                    alert('Error saving order: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error saving order: ' + error);
                console.error('Error:', error);
            })
            .finally(() => {
               
                doneBtn.disabled = false;
                doneBtn.innerHTML = 'Done';
            });
        }

        function confirmCancel() {
            if (confirm('Are you sure you want to cancel the order?')) {
                clearOrder();
            }
        }

        function clearOrder() {
            order.length = 0;
            updateOrderList();
            document.getElementById('moneyInput').value = '';
        }

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