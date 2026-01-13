<?php
// Include database configuration
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name      = trim($_POST['first_name']);
    $last_name       = trim($_POST['last_name']);
    $phone_number    = trim($_POST['phone_number']);
    $wilaya          = trim($_POST['wilaya']);
    $commune         = trim($_POST['commune']);
    $delivery_option = trim($_POST['delivery_option']);
    
    // Hardcoded product info for single product page
    $product_name  = "مدفأة كهربائية 360° بخمس واجهات";
    $product_price = 4000.00;

    // Validate required fields (color removed)
    if (empty($first_name) || empty($last_name) || empty($phone_number) || 
        empty($wilaya) || empty($commune) || empty($delivery_option)) {
        die("All fields are required.");
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        die("Please enter a valid 10-digit phone number.");
    }

    try {
        // Prepare SQL statement (color removed)
        $stmt = $pdo->prepare("INSERT INTO orders 
            (first_name, last_name, phone_number, wilaya, commune, delivery_option, product_name, product_price) 
            VALUES (:first_name, :last_name, :phone_number, :wilaya, :commune, :delivery_option, :product_name, :product_price)");
        
        // Bind parameters
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':wilaya', $wilaya);
        $stmt->bindParam(':commune', $commune);
        $stmt->bindParam(':delivery_option', $delivery_option);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':product_price', $product_price);
        
        // Execute the statement
        if ($stmt->execute()) {
            header("Location: index.php?message=success");
            exit();
        } else {
            echo "Error: Could not save order.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Invalid request method.");
}
?>
