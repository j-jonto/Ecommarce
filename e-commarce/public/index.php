<?php
$pageTitle = "Homepage";
require_once '../includes/header.php';

// Get featured products or recent products
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p
          JOIN categories c ON p.category_id = c.id
          ORDER BY p.id DESC
          LIMIT 8";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get main categories
$query = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name LIMIT 3";
$stmt = $pdo->prepare($query);
$stmt->execute();
$mainCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to <?php echo SITE_NAME; ?></h1>
        <p>Discover our amazing products and shop with confidence</p>
        <a href="#featured-products" class="btn btn-accent">Shop Now</a>
    </div>
</section>

<!-- Featured Categories Section -->
<section class="featured-categories">
    <h2 class="section-title">Browse Categories</h2>
    <div class="categories-container">
        <?php if (count($mainCategories) > 0): ?>
            <?php foreach ($mainCategories as $category): ?>
                <div class="category-card">
                    <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $category['slug']; ?>">
                        <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                        <?php if (!empty($category['description'])): ?>
                            <div class="category-description"><?php echo htmlspecialchars($category['description']); ?></div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured-products" class="featured-products">
    <h2 class="section-title">Featured Products</h2>
    <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="<?php echo SITE_URL; ?>/product.php?id=<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?php echo SITE_URL . '/assets/uploads/' . $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
        <p>No products found.</p>
    <?php endif; ?>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us">
    <h2 class="section-title">Why Choose Us</h2>
    <div class="features-container">
        <div class="feature">
            <div class="feature-icon">🚚</div>
            <h3>Fast Delivery</h3>
            <p>Quick and reliable shipping to your doorstep.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">💯</div>
            <h3>Quality Products</h3>
            <p>We ensure the highest quality for all our products.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">🔒</div>
            <h3>Secure Shopping</h3>
            <p>Your information is always protected.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">💬</div>
            <h3>24/7 Support</h3>
            <p>Our customer service team is always ready to help.</p>
        </div>
    </div>
</section>

<link rel="stylesheet" href="assets/css/index.css">

<?php require_once '../includes/footer.php'; ?>