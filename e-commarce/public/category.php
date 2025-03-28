<?php
// Check if category slug is provided
if (!isset($_GET['slug'])) {
    header("Location: index.php");
    exit;
}

$categorySlug = $_GET['slug'];
$pageTitle = "Category Products";
require_once '../includes/header.php';

// Get category details
$query = "SELECT * FROM categories WHERE slug = :slug";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':slug', $categorySlug);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if category exists
if (!$category) {
    echo displayError("Category not found.");
    require_once '../includes/footer.php';
    exit;
}

// Update page title with category name
$pageTitle = htmlspecialchars($category['name']);

// Get all subcategories of this category
$query = "SELECT * FROM categories WHERE parent_id = :parent_id ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':parent_id', $category['id']);
$stmt->execute();
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products from this category and its subcategories
$categoryIds = [$category['id']];
foreach ($subcategories as $subcategory) {
    $categoryIds[] = $subcategory['id'];
}

$placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p
          JOIN categories c ON p.category_id = c.id
          WHERE p.category_id IN ($placeholders)
          ORDER BY p.name";
$stmt = $pdo->prepare($query);

// Bind values to placeholders
foreach ($categoryIds as $key => $value) {
    $stmt->bindValue($key + 1, $value);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<nav class="breadcrumb">
    <a href="<?php echo SITE_URL; ?>">Home</a> &gt;
    <?php if (!empty($category['parent_id'])): 
        // Get parent category
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $category['parent_id']);
        $stmt->execute();
        $parentCategory = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($parentCategory): ?>
            <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $parentCategory['slug']; ?>">
                <?php echo htmlspecialchars($parentCategory['name']); ?>
            </a> &gt;
        <?php endif;
    endif; ?>
    <span><?php echo htmlspecialchars($category['name']); ?></span>
</nav>

<div class="category-header">
    <h1 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h1>
    <?php if (!empty($category['description'])): ?>
        <div class="category-description">
            <?php echo nl2br(htmlspecialchars($category['description'])); ?>
        </div>
    <?php endif; ?>
</div>

<?php if (count($subcategories) > 0): ?>
    <div class="subcategories">
        <h2>Subcategories</h2>
        <div class="categories-container">
            <?php foreach ($subcategories as $subcategory): ?>
                <div class="category-card">
                    <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $subcategory['slug']; ?>">
                        <div class="category-name"><?php echo htmlspecialchars($subcategory['name']); ?></div>
                        <?php if (!empty($subcategory['description'])): ?>
                            <div class="category-description"><?php echo htmlspecialchars($subcategory['description']); ?></div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="category-products">
    <h2>Products</h2>
    <?php if (count($products) > 0): ?>
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
        <p>No products found in this category.</p>
    <?php endif; ?>
</div>

<style>
.breadcrumb {
    margin-bottom: 30px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}

.breadcrumb a {
    color: var(--primary-color);
    margin-right: 5px;
}

.breadcrumb span {
    color: var(--text-color);
}

.category-header {
    margin-bottom: 40px;
}

.subcategories {
    margin-bottom: 40px;
}

.subcategories h2 {
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.categories-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.category-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-name {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: var(--secondary-color);
}

.category-description {
    color: #666;
    font-size: 0.9rem;
}

.category-products h2 {
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.no-image {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    background-color: #f5f5f5;
    color: #999;
}
</style>

<?php require_once '../includes/footer.php'; ?>