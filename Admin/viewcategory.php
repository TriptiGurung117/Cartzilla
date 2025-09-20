<?php
include 'dbconnection.php';
session_start();

// Only admin
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Add category
if(isset($_POST['add_category'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $query = "INSERT INTO categories (name) VALUES ('$name')";
    if(mysqli_query($conn, $query)){
        $success = "Category added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - CartZilla Admin</title>
      <link rel="stylesheet" type="text/css" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .main-content {
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #127b8e;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Add category form */
        .add-category-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .add-category-form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .add-category-form input[type="text"] {
            padding: 10px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .add-category-form input[type="text"]:focus {
            border-color: #127b8e;
            outline: none;
            box-shadow: 0 0 5px rgba(18,123,142,0.3);
        }

        .add-category-form button {
            padding: 10px 15px;
            background: #127b8e;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .add-category-form button:hover {
            background: #0f6673;
        }

        .success-message { color: #10b981; font-weight: bold; margin-bottom: 15px; }
        .error-message { color: #ef4444; font-weight: bold; margin-bottom: 15px; }

        /* Categories table */
        .categories-table-container {
            width: 100%;
            background: #fff;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .categories-table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .categories-table-container th, 
        .categories-table-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .categories-table-container th {
            background-color: #127b8e;
            color: #fff;
        }

        .categories-table-container tr:hover {
            background-color: #f1f1f1;
        }

        .category-link {
            color: #127b8e;
            font-weight: bold;
            text-decoration: none;
        }

        .category-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<section class="main-content">
    <h1>Manage Categories</h1>

    <!-- Add Category Form -->
    <div class="add-category-form">
        <?php if(isset($success)) echo "<p class='success-message'>$success</p>"; ?>
        <?php if(isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST">
            <label>Category Name:</label>
            <input type="text" name="name" required>
            <button type="submit" name="add_category">Add Category</button>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="categories-table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Total Products</th>
            </tr>
            <?php
            $query = "SELECT c.id, c.name, COUNT(p.id) AS total_products
                      FROM categories c
                      LEFT JOIN products p ON c.id = p.category_id
                      GROUP BY c.id, c.name
                      ORDER BY c.name ASC";
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td><a class='category-link' href='viewcategories.php?category_id={$row['id']}'>{$row['name']}</a></td>
                        <td>{$row['total_products']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</section>

</body>
</html>
