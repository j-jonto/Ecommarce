<?php
// General helper functions

/**
 * Sanitize input data
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a URL
 * @param string $url The URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Generate a secure random token
 * @param int $length Token length
 * @return string The generated token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Display error message
 * @param string $message The error message
 */
function displayError($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

/**
 * Display success message
 * @param string $message The success message
 */
function displaySuccess($message) {
    return "<div class='alert alert-success'>$message</div>";
}

/**
 * Format price with currency symbol
 * @param float $price The price to format
 * @return string The formatted price
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Generate a unique filename for uploads
 * @param string $originalName Original filename
 * @return string The unique filename
 */
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Validate file upload
 * @param array $file The uploaded file ($_FILES array element)
 * @return array Status and message
 */
function validateFileUpload($file) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'Upload failed with error code: ' . $file['error']];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['status' => false, 'message' => 'File is too large. Maximum size is ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileType = $finfo->file($file['tmp_name']);
    
    if (!in_array($fileType, ALLOWED_FILE_TYPES)) {
        return ['status' => false, 'message' => 'Invalid file type. Allowed types: JPG, PNG, GIF, WEBP'];
    }
    
    return ['status' => true, 'message' => 'File is valid'];
}

/**
 * Get all parent categories
 * @param PDO $pdo Database connection
 * @return array List of parent categories
 */
function getParentCategories($pdo) {
    $query = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get category by slug
 * @param PDO $pdo Database connection
 * @param string $slug Category slug
 * @return array|bool Category data or false if not found
 */
function getCategoryBySlug($pdo, $slug) {
    $query = "SELECT * FROM categories WHERE slug = :slug";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':slug', $slug);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get products by category
 * @param PDO $pdo Database connection
 * @param int $categoryId Category ID
 * @return array List of products
 */
function getProductsByCategory($pdo, $categoryId) {
    $query = "SELECT * FROM products WHERE category_id = :category_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get product by ID
 * @param PDO $pdo Database connection
 * @param int $productId Product ID
 * @return array|bool Product data or false if not found
 */
function getProductById($pdo, $productId) {
    $query = "SELECT p.*, c.name as category_name FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Search products function in functions.php
function searchProducts($pdo, $keyword) {
    $searchTerm = "%$keyword%";
    // SQLite compatible query
    $query = "SELECT p.*, c.name as category_name FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.name LIKE :keyword OR p.description LIKE :keyword";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':keyword', $searchTerm);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>