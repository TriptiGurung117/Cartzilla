<?php
// Example: product array (replace with DB query)
$products = [
  ["id"=>1,"name"=>"Wooden Chair","price"=>2500,"image"=>"chair.jpg"],
  ["id"=>2,"name"=>"Modern Sofa","price"=>18500,"image"=>"sofa.jpg"],
  ["id"=>3,"name"=>"Dining Table","price"=>12000,"image"=>"table.jpg"],
  ["id"=>4,"name"=>"Cupboard","price"=>8000,"image"=>"cupboard.jpg"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products – Cartzilla</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    .product-card {
      border:1px solid #eee;
      border-radius:10px;
      transition:0.3s;
      overflow:hidden;
      background:#fff;
    }
    .product-card:hover {
      box-shadow:0 4px 10px rgba(0,0,0,0.1);
      transform: translateY(-5px);
    }
    .product-card img {
      width:100%;
      height:220px;
      object-fit:cover;
    }
    .product-card .card-body {
      padding:12px;
    }
    .product-price {
      font-size:18px;
      font-weight:bold;
      color:#e53935;
    }
    .add-btn {
      background:#ff5722;
      border:none;
      color:#fff;
      border-radius:6px;
      padding:6px 12px;
    }
    .add-btn:hover {
      background:#e64a19;
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="mb-4">Products</h2>
  <div class="row g-4">
    <?php foreach($products as $p): ?>
      <div class="col-md-3 col-sm-6">
        <div class="product-card">
          <img src="images/<?= htmlspecialchars($p['image']); ?>" alt="<?= htmlspecialchars($p['name']); ?>">
          <div class="card-body">
            <h6 class="card-title"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="product-price">Rs <?= number_format($p['price']); ?></p>
            <form method="post" action="cart.php">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="id" value="<?= $p['id']; ?>">
              <input type="hidden" name="name" value="<?= htmlspecialchars($p['name']); ?>">
              <input type="hidden" name="price" value="<?= $p['price']; ?>">
              <input type="hidden" name="image" value="<?= htmlspecialchars($p['image']); ?>">
              <button type="submit" class="add-btn w-100">Add to Cart</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
