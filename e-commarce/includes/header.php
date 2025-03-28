<?php
// Include configuration and database connection
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';
require_once 'cart_functions.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Initialize cart
initCart();

// Get all parent categories for the navigation menu
$query = "SELECT id, name, slug FROM categories WHERE parent_id IS NULL ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/e-commarce/public/assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/products.php">Products</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-shopping-cart"></i> Cart
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="container py-4">
        <div class="container">