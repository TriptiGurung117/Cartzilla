<?php 
include 'dbconnection.php';
session_start();

// Ensure only admin can access
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - CartZilla Admin</title>
    <link rel="stylesheet" type="text/css" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .main-content h1 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 3px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #127b8e; color: #fff; }
        table tr:hover { background-color: #f1f1f1; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-completed { color: #10b981; font-weight: bold; }
        .status-cancelled { color: #ef4444; font-weight: bold; }
        .view-items-btn { background: #127b8e; color: #fff; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .view-items-btn:hover { background: #0f6673; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<section class="main-content">
    <h1>Orders</h1>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT o.id, o.user_id, u.Name AS customer_name, o.total_price, o.status
                      FROM orders o
                      LEFT JOIN users u ON o.user_id = u.Id
                      ORDER BY o.id DESC";

            $result = mysqli_query($conn, $query);

            if(!$result){
                echo "<tr><td colspan='5'>Error: ".mysqli_error($conn)."</td></tr>";
            } else {
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        $statusClass = '';
                        if(strtolower($row['status']) == 'pending') $statusClass = 'status-pending';
                        elseif(strtolower($row['status']) == 'completed') $statusClass = 'status-completed';
                        elseif(strtolower($row['status']) == 'cancelled') $statusClass = 'status-cancelled';

                        echo '<tr>';
                        echo '<td>'.$row['id'].'</td>';
                        echo '<td>'.($row['customer_name'] ?? 'Unknown').'</td>';
                        echo '<td>Rs. '.$row['total_price'].'</td>';
                        echo '<td class="'.$statusClass.'">'.$row['status'].'</td>';
                        
                        // View Items button
                        echo '<td>
                                <form method="get" action="order_items.php" style="margin:0;">
                                    <input type="hidden" name="order_id" value="'.$row['id'].'">
                                    <button type="submit" class="view-items-btn">View Items</button>
                                </form>
                              </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" style="text-align:center;">No orders found</td></tr>';
                }
            }
            ?>
        </tbody>
    </table>

</section>

</body>
</html>
