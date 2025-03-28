<?php
ob_start(); // Start output buffering
$pageTitle = "Add Product";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php'; // Include the database connection

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get all categories for dropdown
$query = "SELECT c.*, p.name as parent_name 
          FROM categories c
          LEFT JOIN categories p ON c.parent_id = p.id
          ORDER BY COALESCE(p.name, c.name), c.name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $price = $_POST['price'] ?? 0.0;

    try {
        $query = "INSERT INTO products (category_id, name, description, price) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$category_id, $name, $description, $price]);

        // Redirect or display success message
        header('Location: products.php?success=Product added successfully');
        exit;
    } catch (PDOException $e) {
        echo "Error adding product: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <h2>Add New Product</h2>
    <form method="post">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" name="name" id="name" required class="form-control">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="text" name="price" id="price" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<?php
require_once 'includes/admin_footer.php'; 
ob_end_flush(); // End output buffering
?>