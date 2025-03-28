<?php
ob_start();
$pageTitle = "Products";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php'; // Include functions.php to access formatPrice

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get all products with their categories
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productId = (int)$_POST['product_id'];

    try {
        // Get product image path
        $query = "SELECT image_path FROM products WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete product
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->execute();

        // Delete product image if exists
        if (!empty($product['image_path'])) {
            $imagePath = ROOT_DIR . '/public/assets/uploads/' . $product['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $success = "Product deleted successfully.";

        // Redirect to refresh the page
        header("Location: products.php?success=" . urlencode($success));
        exit;
    } catch (PDOException $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}

// Set success message from URL parameter
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
?>

<div class="page-header">
    <h1 class="page-title">Manage Products</h1>
    <a href="product_add.php" class="btn">Add New Product</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (count($products) > 0): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td class="product-image-cell">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo formatPrice($product['price']); ?></td> <!-- Correct use of formatPrice -->
                    <td><?php echo ucfirst(str_replace('_', ' ', $product['stock_status'])); ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm">Edit</a>

                            <form method="post" action="" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="delete_product" class="btn btn-sm btn-danger delete-btn">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No products found. <a href="product_add.php">Add a product</a> to get started.</p>
<?php endif; ?>

<style>
.product-image-cell {
    width: 80px;
}

.product-image-cell img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.no-image {
    width: 60px;
    height: 60px;
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 0.7rem;
    color: #999;
    border-radius: 4px;
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>