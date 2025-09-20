<?php
include 'dbconnection.php';
session_start();

// Ensure only admin can access
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle form submission
if(isset($_POST['add_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Handle image upload
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image_name = time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/".$image_name);
    }

    $query = "INSERT INTO products (name, price, category_id, description, image) 
              VALUES ('$name','$price','$category','$description','$image_name')";
    mysqli_query($conn, $query);
    $success = "Product added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - CartZilla Admin</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .main-content {
            padding: 30px;
            max-width: 700px;
            margin: auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        h1 { margin-bottom: 20px; text-align: center; color: #127b8e; }
        form label { font-weight: bold; margin-top: 10px; display: block; }
        form input, form select, form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 15px;
            font-size: 14px;
        }
        form button {
            background-color: #127b8e;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        form button:hover {
            background-color: #0f6673;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<section class="main-content">
    <h1>Add New Product</h1>

    <?php if(isset($success)) echo "<div class='success-msg'>$success</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="name" required>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="">--Select Category--</option>
            <?php
            $categories = mysqli_query($conn, "SELECT * FROM categories");
            while($cat = mysqli_fetch_assoc($categories)){
                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
            }
            ?>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="4" placeholder="Enter product description"></textarea>

        <label>Product Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit" name="add_product">Add Product</button>
    </form>
</section>
</body>
</html>
