<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // First, get the image name to delete it from server
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Delete the product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        // Delete the associated image file
        $image_path = "../images/" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
}

header("Location: manage_products.php");
exit();
?>