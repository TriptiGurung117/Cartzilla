<?php
include 'dbconnection.php';
session_start();

$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!$category) {
    echo "No category selected.";
    exit;
}

// Fetch products of this category
$stmt = $conn->prepare("SELECT * FROM products WHERE category=?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($category); ?> Products</title>
<link rel="stylesheet" href="style.css">
<style>
/* Category page specific styling */
h2 {
    text-align: center;
    margin: 40px 0 20px;
    font-size: 2rem;
}

.carousel-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
    padding-bottom: 40px;
}

.product-card {
    width: 250px;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    position: relative;
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: contain;
}

.price {
    font-weight: bold;
    margin: 10px 0;
}

.add-to-cart, .buy-now {
    margin-top: 10px;
    padding: 10px;
    width: 100%;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

.add-to-cart {
    background-color: #1a1f2b;
    color: #fff;
    margin-bottom: 5px;
}

.add-to-cart:hover {
    background-color: #000;
}

.buy-now {
    background-color: #ff6f61;
    color: #fff;
}

.buy-now:hover {
    background-color: #ff3b2e;
}
</style>
</head>
<body>

<h2><?php echo htmlspecialchars($category); ?> Products</h2>

<div class="carousel-container">
<?php while ($row = $result->fetch_assoc()) { ?>
    <div class="product-card">
        <img src="images/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p class="price">$<?php echo $row['price']; ?></p>
        <form action="checkout.php" method="post">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
            <input type="hidden" name="image" value="<?php echo $row['image']; ?>">
            <input type="hidden" name="qty" value="1">
            <button class="add-to-cart" type="submit">Add to Cart</button>
            <button class="buy-now" type="submit">Buy Now</button>
        </form>
    </div>
<?php } ?>
</div>

</body>
</html>
