<?php
session_start();
include "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$items = $_SESSION['cart'] ?? [];
if (empty($items)) {
    echo "<h2>Your cart is empty!</h2>";
    exit;
}

// Calculate total
$total_price = 0;
foreach ($items as $item) {
    $total_price += $item['price'] * $item['qty'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    foreach ($items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
        $stmt->execute();
    }

    // Empty cart
    $_SESSION['cart'] = [];

    // Redirect to payment page
    header("Location: payments.php?order_id=" . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Cartzilla</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
.checkout-container { max-width: 700px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.checkout-container h2 { text-align: center; margin-bottom: 30px; color: #dc3545; }
.checkout-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.checkout-table th, .checkout-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: center; }
.checkout-table th { background-color: #1a1f2b; color: #fff; }
.checkout-container button { width: 100%; padding: 12px; background-color: #dc3545; border: none; border-radius: 6px; color: #fff; font-weight: bold; cursor: pointer; }
.checkout-container button:hover { background-color: #b71c1c; }
.total-price { text-align: right; font-weight: bold; margin-top: 10px; }
</style>
</head>
<body>

<div class="checkout-container">
    <h2>Checkout</h2>

    <table class="checkout-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= $item['qty']; ?></td>
                <td>Rs <?= number_format($item['price'],2); ?></td>
                <td>Rs <?= number_format($item['price'] * $item['qty'],2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-price">
        Total: Rs <?= number_format($total_price,2); ?>
    </div>

    <form method="post">
        <button type="submit">Place Order & Pay</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
