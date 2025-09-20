<?php
include 'dbconnection.php';
session_start();

// Only admin can access
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Get order_id
$order_id = intval($_GET['order_id'] ?? 0);
if($order_id <= 0){
    echo "Invalid Order ID.";
    exit();
}

// Fetch order details
$order_query = "SELECT o.id, u.Name AS customer_name, o.total_price, o.status
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.Id
                WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);
if(mysqli_num_rows($order_result) == 0){
    echo "Order not found.";
    exit();
}
$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$items_query = "SELECT oi.product_id, p.name AS product_name, p.description, p.image, oi.quantity, oi.price
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Items - CartZilla Admin</title>
    <link rel="stylesheet" type="text/css" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .main-content { padding: 30px; }
        h1 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 3px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #127b8e; color: #fff; }
        table tr:hover { background-color: #f1f1f1; }
        .back-btn { padding: 8px 15px; background: #127b8e; color: #fff; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background: #0f6673; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<section class="main-content">
    <h1>Order #<?php echo $order['id']; ?> Items</h1>
    <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?></p>
    <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
    <p><strong>Total Price:</strong> Rs. <?php echo $order['total_price']; ?></p>

    <h2>Products in this Order</h2>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price (per unit)</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(mysqli_num_rows($items_result) > 0){
                while($item = mysqli_fetch_assoc($items_result)){
                    $subtotal = $item['quantity'] * $item['price'];
                    echo "<tr>";
                    echo "<td><img src='images/".$item['image']."' class='product-img' alt=''></td>";
                    echo "<td>".$item['product_name']."</td>";
                    echo "<td>".$item['description']."</td>";
                    echo "<td>".$item['quantity']."</td>";
                    echo "<td>Rs. ".$item['price']."</td>";
                    echo "<td>Rs. ".$subtotal."</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">No items found for this order</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <br>
    <a href="orders.php" class="back-btn">Back to Orders</a>
</section>

</body>
</html>
