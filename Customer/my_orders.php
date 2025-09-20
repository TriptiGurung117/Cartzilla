<?php
include 'dbconnection.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? 'User';

// Fetch orders with items
$sql = "SELECT o.id AS order_id, o.total_price, o.status, 
               p.name AS product_name, oi.quantity, oi.price AS item_price
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['total_price'] = $row['total_price'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['items'][] = [
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['item_price']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
.navbar-custom { background-color: #1a1f2b; }
.navbar-custom .navbar-brand { color: #dc3545; font-weight: bold; font-size: 2rem; }
.navbar-custom .btn-outline-light { color: #fff; }
.navbar-custom .btn-outline-light:hover { color: #dc3545; }
.dropdown-menu { min-width: 200px; }

.orders-container { max-width: 1000px; margin: 30px auto; }
.orders-container h2 { text-align: center; margin-bottom: 20px; color: #dc3545; }
.card { margin-bottom: 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.card-header { background: #1a1f2b; color: #fff; font-weight: bold; }
.status-Pending { color: orange; font-weight: bold; }
.status-Shipped { color: green; font-weight: bold; }
.status-Cancelled { color: red; font-weight: bold; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    <a class="navbar-brand" href="index.php">Cartzilla</a>
    <div class="ms-auto">
      <div class="dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <?= htmlspecialchars($username); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php">View / Edit Profile</a></li>
          <li><a class="dropdown-item" href="my_orders.php">My Orders</a></li>
          <li><a class="dropdown-item" href="payments.php">Payments</a></li>
          <li><a class="dropdown-item" href="index.php">Home</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Orders -->
<div class="orders-container">
    <h2>My Orders</h2>
    <?php if(!empty($orders)): ?>
        <?php foreach($orders as $order_id => $order): ?>
        <div class="card">
            <div class="card-header">
                Order #<?= $order_id; ?> 
                | Total: $<?= number_format($order['total_price'],2); ?> 
                | Status: <span class="status-<?= $order['status']; ?>"><?= $order['status']; ?></span>
                <a href="payments.php?order_id=<?= $order_id; ?>" class="btn btn-sm btn-danger float-end">Pay Now</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td><?= number_format($item['price'],2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">No orders found!</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  