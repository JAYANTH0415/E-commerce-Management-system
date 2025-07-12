<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Fetch product data if ID is provided
$product = null;
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: manage_products.php");
        exit();
    }
}

// Handle form submission
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Handle image upload if new image is provided
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/$image");
    } else {
        $image = $_POST['existing_image'];
    }
    
    // Update product in database
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $description, $image, $id]);
    
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #4CAF50;
        }
        .image-preview {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>
        <?php if ($product): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= $product['image'] ?>">
            
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?= $product['price'] ?>" required>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($product['description']) ?></textarea>
            
            <label>Current Image:</label>
            <img src="../images/<?= $product['image'] ?>" class="image-preview" alt="Current Product Image">
            
            <label for="image">New Image (Leave blank to keep current):</label>
            <input type="file" name="image" id="image">
            
            <button type="submit" name="update_product">Update Product</button>
        </form>
        <div class="back-link">
            <a href="manage_products.php">Back to Manage Products</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>