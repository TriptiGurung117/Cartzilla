<?php
session_start();
include 'dbconnection.php';
// if user is not logged in, redirect to login when trying checkout/buy
if (isset($_POST['action']) && $_POST['action'] === 'buy_now') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = "cart.php"; // so it comes back here
        header("Location: login.php");
        exit;
    } else {
        // here you would normally create an order
        header("Location: checkout.php");
        exit;
    }
}

/*
 Session cart structure:
 $_SESSION['cart'] = [
   'product_id' => ['id'=>..., 'name'=>..., 'price'=>..., 'image'=>..., 'qty'=>...],
   ...
 ];
*/

// ADD item
if (isset($_POST['action']) && $_POST['action'] === 'add') {
  $id    = $_POST['id'] ?? '';
  $name  = $_POST['name'] ?? '';
  $price = (float)($_POST['price'] ?? 0);
  $image = $_POST['image'] ?? '';
  if ($id !== '') {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$id])) {
      $_SESSION['cart'][$id]['qty'] += 1;
    } else {
      $_SESSION['cart'][$id] = ['id'=>$id,'name'=>$name,'price'=>$price,'image'=>$image,'qty'=>1];
    }
  }
  header('Location: cart.php');
  exit;
}

// UPDATE qty
if (isset($_POST['action']) && $_POST['action'] === 'update') {
  $id  = $_POST['id'] ?? '';
  $qty = max(1, (int)($_POST['qty'] ?? 1));
  if ($id !== '' && isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty'] = $qty;
  }
  header('Location: cart.php');
  exit;
}

// REMOVE one
if (isset($_GET['remove'])) {
  $id = $_GET['remove'];
  if (isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]);
  header('Location: cart.php');
  exit;
}

// CLEAR all
if (isset($_GET['clear'])) {
  unset($_SESSION['cart']);
  header('Location: cart.php');
  exit;
}

// compute totals
$items = $_SESSION['cart'] ?? [];
$grand = 0;
foreach ($items as $it) $grand += $it['price'] * $it['qty'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart – Cartzilla</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .cart-header { background:#f8f9fa; border-bottom:1px solid #eee; }
    .cart-table img{ width:80px; height:80px; object-fit:cover; border-radius:6px; }
    .cart-actions a { text-decoration:none; }
    .cart-total-box{ background:#1f2732; color:#fff; border-radius:10px; padding:16px; }
    .btn-dark{ background:#000; border-color:#000; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg cart-header">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold text-danger" href="index.php">Cartzilla</a>
      <div class="ms-auto">
        <a href="index.php" class="btn btn-outline-dark btn-sm">Continue shopping</a>
        <?php if(!empty($items)): ?>
          <a href="cart.php?clear=1" class="btn btn-outline-danger btn-sm ms-2" onclick="return confirm('Clear all items?')">Clear All</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <h2 class="mb-3">Your Cart</h2>

    <?php if(empty($items)): ?>
      <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle cart-table">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Item</th>
              <th>Image</th>
              <th>Price</th>
              <th style="width:180px;">Quantity</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $i=1;
            foreach($items as $it):
              $sub = $it['price'] * $it['qty'];
          ?>
            <tr>
              <td><?= $i++; ?></td>
              <td><?= htmlspecialchars($it['name']); ?></td>
              <td><img src="images/<?= htmlspecialchars($it['image']); ?>" alt=""></td>
              <td>Rs <?= number_format($it['price'],2); ?></td>
              <td>
                <form method="post" class="d-flex gap-2">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']); ?>">
                  <input type="number" name="qty" min="1" value="<?= (int)$it['qty']; ?>" class="form-control form-control-sm" style="max-width:90px;">
                  <button class="btn btn-dark btn-sm" type="submit">Update</button>
                </form>
              </td>
              <td>Rs <?= number_format($sub,2); ?></td>
              <td class="cart-actions">
                <a href="cart.php?remove=<?= urlencode($it['id']); ?>" class="text-danger" onclick="return confirm('Remove this item?')">
                  <i class="bi bi-trash"></i> Remove
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div></div>
        <div class="cart-total-box">
          <div class="d-flex justify-content-between align-items-center">
            <strong>Grand Total:</strong>
            <span class="fs-5">Rs <?= number_format($grand,2); ?></span>
          </div>
          <div class="mt-3 text-end">
            <form method="post">
              <input type="hidden" name="action" value="buy_now">
              <button type="submit" class="btn btn-light btn-sm">Buy Now</button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
