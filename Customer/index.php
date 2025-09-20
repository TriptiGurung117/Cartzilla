<?php
// db connection
include "dbconnection.php";

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search   = isset($_GET['search']) ? $_GET['search'] : null;
$products = [];

if ($search) {
    // search products
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $products = $stmt->fetchAll();
} elseif ($category) {
    // fetch by category
    $stmt = $pdo->prepare("
        SELECT p.* FROM products p
        JOIN subcategories s ON p.subcategory_id = s.id
        JOIN categories c ON s.category_id = c.id
        WHERE LOWER(c.name) = ?
    ");
    $stmt->execute([strtolower($category)]);
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cartzilla - Furniture Store</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- HEADER -->
<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3 text-warning" href="index.php">Cartzilla</a>

    <!-- Search -->
    <form class="d-flex mx-auto w-50" action="index.php" method="get">
      <input class="form-control rounded-0" type="search" placeholder="Search store" name="search">
      <button class="btn btn-outline-dark rounded-0" type="submit"><i class="bi bi-search"></i></button>
    </form>

    <!-- Account + Cart -->
    <div>
      <a href="register.php" class="me-3">Register</a>
      <a href="login.php">Log in</a>
      <a href="cart.php" class="ms-3"><i class="bi bi-bag"></i></a>
    </div>
  </div>
</header>

<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg" style="background-color:#f5c400;">
  <div class="container">
    <ul class="navbar-nav mx-auto">
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=furniture">FURNITURE</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=bedding">BEDDING</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=home-items">HOME ITEMS</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=storage">STORAGE</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=brands">BRANDS</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=new">NEW</a></li>
      <li class="nav-item"><a class="nav-link text-dark fw-bold" href="index.php?category=projects">PROJECTS</a></li>
    </ul>
  </div>
</nav>

<!-- HERO BANNER -->
<section class="hero">
  <img src="images/hero-banner.jpg" class="img-fluid w-100" alt="Hero Banner">
</section>

<!-- FEATURED CATEGORIES -->
<section class="container my-5">
  <!-- FEATURED CATEGORIES -->
<section class="container my-5">
  <h3 class="text-center mb-4">FEATURED CATEGORIES</h3>
  <div class="row text-center">
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=bedroom">
          <img src="images/bedroom.jpg" alt="Bedroom">
          <div class="overlay">BEDROOM</div>
        </a>
      </div>
    </div>
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=living-room">
          <img src="images/livingroom.jpg" alt="Living Room">
          <div class="overlay">LIVING</div>
        </a>
      </div>
    </div>
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=kitchen">
          <img src="images/kitchen.jpg" alt="Dining">
          <div class="overlay">DINING</div>
        </a>
      </div>
    </div>
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=office">
          <img src="images/office.jpg" alt="Office">
          <div class="overlay">OFFICE</div>
        </a>
      </div>
    </div>
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=decoration">
          <img src="images/decoration.jpg" alt="Decoration">
          <div class="overlay">DECORATION</div>
        </a>
      </div>
    </div>
    
    <div class="col-md-2 mb-4">
      <div class="category-card">
        <a href="index.php?category=bathroom">
          <img src="images/bathroom.jpg" alt="Bathroom">
          <div class="overlay">BATHROOM</div>
        </a>
      </div>
    </div>

  </div>
</section>

</section>

<!-- PRODUCT LISTING -->
<section class="container my-5">
  <?php if ($search): ?>
    <h3>Search Results for "<?= htmlspecialchars($search); ?>"</h3>
  <?php elseif ($category): ?>
    <h3><?= ucfirst($category); ?> Products</h3>
  <?php endif; ?>

  <div class="row">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $prod): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100">
            <img src="images/<?= $prod['image']; ?>" class="card-img-top" alt="<?= $prod['name']; ?>">
            <div class="card-body text-center">
              <h5><?= $prod['name']; ?></h5>
              <p>$<?= $prod['price']; ?></p>
              <form action="cart.php" method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= $prod['id']; ?>">
                <input type="hidden" name="name" value="<?= $prod['name']; ?>">
                <input type="hidden" name="price" value="<?= $prod['price']; ?>">
                <input type="hidden" name="image" value="<?= $prod['image']; ?>">
                <button type="submit" class="btn btn-dark">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php elseif ($search || $category): ?>
      <p>No products found.</p>
    <?php endif; ?>
  </div>
</section>

<!-- FOOTER -->
<footer class="custom-footer">
  <div class="container footer-container">
    <div class="footer-column">
      <h3>INFORMATION</h3>
      <p>Index Furniture Nepal is an exclusive franchise of Index Living Mall, Thailand, 
      one of the largest manufacturers and retailers of furniture and lifestyle products in Asia 
      with showrooms across Thailand, Lao PDR, Vietnam, Malaysia, Russia, Myanmar, Cambodia, Pakistan and Indonesia.</p>
    </div>
    <div class="footer-column">
      <h3>MY ACCOUNT</h3>
      <a href="#">Orders</a>
      <a href="#">Addresses</a>
      <a href="#">Shopping cart</a>
    </div>
    <div class="footer-column">
      <h3>CUSTOMER SERVICE</h3>
      <a href="#">Contact us</a>
      <a href="#">Projects</a>
      <a href="#">Sitemap</a>
      <a href="#">Apply for vendor account</a>
    </div>
    <div class="footer-column">
      <h3>FOLLOW US</h3>
      <form class="subscribe-box">
        <input type="email" placeholder="Enter your email here...">
        <button type="submit">Subscribe</button>
      </form>
    </div>
  </div>
  <div class="footer-bottom text-center">
    Copyright © 2025 Index Furniture Nepal. All rights reserved.
  </div>
</footer>


</body>
</html>
