<?php
session_start();
include "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;

// Fetch order
$stmt = $conn->prepare("SELECT total_price, status FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<h2>Order not found!</h2>";
    exit;
}

$error_message = '';
$show_gateway_login = false;
$payment_method = '';
$gateway_user = '';

// Step 1: User selects payment method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 'method';

    if ($step === 'method') {
        $payment_method = $_POST['payment_method'] ?? '';
        if ($payment_method === 'eSewa' || $payment_method === 'Khalti') {
            $show_gateway_login = true; // show login/ID form next
        } else {
            $error_message = "Invalid payment method selected!";
        }
    } elseif ($step === 'gateway') {
        $payment_method = $_POST['payment_method'];
        $gateway_user = trim($_POST['gateway_user'] ?? '');
        if (empty($gateway_user)) {
            $error_message = "Please enter your {$payment_method} ID to proceed.";
            $show_gateway_login = true;
        } else {
            // Simulate payment success
            $conn->begin_transaction();
            try {
                // Insert payment
                $stmt = $conn->prepare("INSERT INTO payments (user_id, order_id, amount, method, status) VALUES (?, ?, ?, ?, ?)");
                $status = 'completed';
                $stmt->bind_param("iidsi", $_SESSION['user_id'], $order_id, $order['total_price'], $payment_method, $status);
                $stmt->execute();

                // Update order
                $stmt = $conn->prepare("UPDATE orders SET payment_method = ?, status = 'Paid' WHERE id = ?");
                $stmt->bind_param("si", $payment_method, $order_id);
                $stmt->execute();

                $conn->commit();

                // Show receipt
                echo "<div style='max-width:600px;margin:50px auto;padding:20px;border:1px solid #ccc;border-radius:10px;background:#f9f9f9;'>
                        <h2>Payment Successful!</h2>
                        <p><strong>Order ID:</strong> {$order_id}</p>
                        <p><strong>Amount Paid:</strong> Rs " . number_format($order['total_price'],2) . "</p>
                        <p><strong>Payment Method:</strong> {$payment_method}</p>
                        <p><strong>{$payment_method} ID:</strong> {$gateway_user}</p>
                        <p><strong>Status:</strong> Completed</p>
                        <p><a href='index.php'>Back to Home</a></p>
                      </div>";
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Payment failed. Please try again.";
                $show_gateway_login = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment - Furniture Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://khalti.com/static/khalti-checkout.js"></script>
<style>
body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
.payment-container { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.payment-container h2 { text-align: center; margin-bottom: 20px; color: #dc3545; }
.payment-container form button { width: 100%; margin-top: 15px; padding: 12px; border: none; border-radius: 6px; background-color: #dc3545; color: #fff; font-weight: bold; cursor: pointer; }
.payment-container form button:hover { background-color: #b71c1c; }
.error-message { color: red; margin-top: 10px; }
</style>
</head>
<body>

<div class="payment-container">
    <h2>Pay for Order #<?= $order_id ?></h2>
    <p>Total Amount: Rs <?= number_format($order['total_price'],2) ?></p>

    <?php if(!empty($error_message)) : ?>
        <div class="error-message"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if(!$show_gateway_login): ?>
    <!-- Step 1: Select Payment Method -->
    <form method="post">
        <input type="hidden" name="step" value="method">
        <label>Select Payment Method:</label>
        <select name="payment_method" class="form-control" required>
            <option value="">-- Choose Payment Method --</option>
            <option value="eSewa">eSewa</option>
            <option value="Khalti">Khalti</option>
        </select>
        <button type="submit">Next</button>
    </form>
    <?php else: ?>
    <!-- Step 2: Enter Gateway ID for eSewa or Khalti simulation -->
    <?php if($payment_method === 'eSewa'): ?>
    <form method="post">
        <input type="hidden" name="step" value="gateway">
        <input type="hidden" name="payment_method" value="eSewa">
        <label>Enter eSewa ID:</label>
        <input type="text" name="gateway_user" class="form-control" value="<?= htmlspecialchars($gateway_user) ?>" required>
        <button type="submit">Pay Now</button>
    </form>
    <?php else: ?>
    <p>Click the button below to pay via Khalti (simulation).</p>
    <button type="button" class="btn btn-danger" onclick="payKhalti()">Pay with Khalti</button>

    <script>
    function payKhalti(){
        var config = {
            "publicKey": "YOUR_KHALTI_PUBLIC_KEY",
            "productIdentity": "<?= $order_id ?>",
            "productName": "Order #<?= $order_id ?>",
            "productUrl": "https://yourdomain.com",
            "paymentPreference": ["KHALTI"],
            "eventHandler": {
                onSuccess (payload) {
                    alert("Payment Successful! Simulate server verification next.");
                    // Here you would send payload.token to khalti_verify.php for verification
                    window.location.reload(); // reload to simulate showing receipt
                },
                onError (error) { console.log(error); },
                onClose () { console.log('Widget closed'); }
            }
        };
        var checkout = new KhaltiCheckout(config);
        checkout.show({amount: <?= $order['total_price'] ?> * 100}); // amount in paisa
    }
    </script>
    <?php endif; ?>
    <?php endif; ?>

</div>

</body>
</html>
