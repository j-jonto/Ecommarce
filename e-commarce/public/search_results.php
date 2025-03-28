<?php
$pageTitle = "Search Results";
require_once '../includes/header.php';

// Get search keyword
$keyword = isset($_GET['keyword']) ? sanitize($_GET['keyword']) : '';

// Redirect if no keyword
if (empty($keyword)) {
    header("Location: " . SITE_URL);
    exit;
}

// Search products
$searchTerm = "%$keyword%";
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p 
          JOIN categories c ON p.category_id = c.id
          WHERE p.name LIKE :keyword 
          OR p.description LIKE :keyword 
          OR p.sku LIKE :keyword";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':keyword', $searchTerm);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Search Results for "<?php echo htmlspecialchars($keyword); ?>"</h1>

<?php if (count($products) > 0): ?>
    <p>Found <?php echo count($products); ?> result(s) for your search.</p>
    
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="<?php echo SITE_URL; ?>/product.php?id=<?php echo $product['id']; ?>">
                    <div class="product-image">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-category">
                            <span>Category: </span>
                            <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $product['category_slug']; ?>">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </a>
                        </div>
                    </div>
                </a>
                <a href="<?php echo SITE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-results">
        <p>No products found matching your search criteria.</p>
        <a href="<?php echo SITE_URL; ?>" class="btn">Return to Home</a>
    </div>
<?php endif; ?>

<style>
.no-results {
    text-align: center;
    padding: 50px 0;
}

.no-image {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    background-color: #f5f5f5;
    color: #999;
}
</style>

<?php require_once '../includes/footer.php'; ?>