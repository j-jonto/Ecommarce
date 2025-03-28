<?php
$pageTitle = "Edit Product";
require_once 'includes/admin_header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = (int)$_GET['id'];
$errors = [];
$success = false;

// Get product details
$query = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $productId);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $sku = isset($_POST['sku']) ? sanitize($_POST['sku']) : '';
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $stockStatus = isset($_POST['stock_status']) ? sanitize($_POST['stock_status']) : 'in_stock';
    
    // Validate form data
    if (empty($name)) {
        $errors[] = "Product name is required.";
    }
    
    if ($price <= 0) {
        $errors[] = "Product price must be greater than zero.";
    }
    
    if ($categoryId <= 0) {
        $errors[] = "Please select a category.";
    }
    
    // Handle image upload
    $imagePath = $product['image_path']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileValidation = validateFileUpload($_FILES['image']);
        
        if (!$fileValidation['status']) {
            $errors[] = $fileValidation['message'];
        } else {
            // Generate unique filename
            $uniqueFilename = generateUniqueFilename($_FILES['image']['name']);
            $uploadPath = ROOT_DIR . '/public/assets/uploads/' . $uniqueFilename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Delete old image if exists
                if (!empty($product['image_path'])) {
                    $oldImagePath = ROOT_DIR . '/public/assets/uploads/' . $product['image_path'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $imagePath = $uniqueFilename;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }
    
    // Update product if no errors
    if (empty($errors)) {
        try {
            $query = "UPDATE products SET name = :name, description = :description, price = :price, 
                      sku = :sku, category_id = :category_id, stock_status = :stock_status, 
                      image_path = :image_path WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':sku', $sku);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':stock_status', $stockStatus);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->bindParam(':id', $productId);
            $stmt->execute();
            
            $success = true;
            
            // Update product data to show updated values
            $product['name'] = $name;
            $product['description'] = $description;
            $product['price'] = $price;
            $product['sku'] = $sku;
            $product['category_id'] = $categoryId;
            $product['stock_status'] = $stockStatus;
            $product['image_path'] = $imagePath;
        } catch (PDOException $e) {
            $errors[] = "An error occurred while updating the product.";
            
            if (DEBUG_MODE) {
                $errors[] = $e->getMessage();
            }
        }
    }
}

// Get categories for select dropdown
$query = "SELECT id, name FROM categories ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Edit Product</h1>
    <a href="products.php" class="btn btn-secondary">Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">Product updated successfully.</div>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data" class="validated-form">
    <div class="form-row">
        <div class="form-col">
            <div class="form-group">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
        </div>
        
        <div class="form-col">
            <div class="form-group">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-col">
            <div class="form-group">
                <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
                <input type="text" id="sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku']); ?>">
                <small>Optional unique identifier for the product.</small>
            </div>
        </div>
        
        <div class="form-col">
            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    
    <div class="form-row">
        <div class="form-col">
            <div class="form-group">
                <label for="stock_status" class="form-label">Stock Status</label>
                <select id="stock_status" name="stock_status" class="form-control">
                    <option value="in_stock" <?php echo $product['stock_status'] == 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                    <option value="out_of_stock" <?php echo $product['stock_status'] == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    <option value="backorder" <?php echo $product['stock_status'] == 'backorder' ? 'selected' : ''; ?>>On Backorder</option>
                </select>
            </div>
        </div>
        
        <div class="form-col">
            <div class="form-group">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small>Acceptable formats: JPG, PNG, GIF. Max size: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB</small>
                <small>Leave empty to keep the current image.</small>
            </div>
            <div id="image-preview" class="image-preview" <?php echo empty($product['image_path']) ? 'style="display: none;"' : ''; ?>>
                <?php if (!empty($product['image_path'])): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $product['image_path']; ?>" alt="Product Image">
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Update Product</button>
</form>

<?php require_once 'includes/admin_footer.php'; ?>