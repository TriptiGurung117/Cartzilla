<?php
include 'dbconnection.php';
session_start();

// Ensure admin access
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products - CartZilla Admin</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .main-content { 
            padding: 30px; 
        }
        h1 { 
            margin-bottom: 20px; 
            color: #127b8e; 
            text-align: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 products per row */
            gap: 20px;
            margin-top: 40px;
        }

        .product-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .product-card .info {
            padding: 15px;
            flex: 1;
        }

        .product-card .info h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .product-card .info p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
        }

        /* Responsive for tablets */
        @media (max-width: 900px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Responsive for mobile */
        @media (max-width: 600px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <section class="main-content">
        <h1>All Products</h1>
        <div class="products-grid">
            <?php
            $query = "SELECT p.*, c.name AS category_name 
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      ORDER BY p.id DESC";
            $result = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $img = !empty($row['image']) ? 'images/'.$row['image'] : 'images/no-image.png';
                    echo '<div class="product-card">';
                    echo '<img src="'.$img.'" alt="'.$row['name'].'">';
                    echo '<div class="info">';
                    echo '<h3>'.$row['name'].'</h3>';
                    echo '<p>Category: '.$row['category_name'].'</p>';
                    echo '<p>Price: Rs. '.$row['price'].'</p>';
                    echo '<p>'.$row['description'].'</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p style='text-align:center;'>No products found!</p>";
            }
            ?>
        </div>
    </section>
</body>
</html>
