<?php
ob_start(); // Start output buffering
$pageTitle = "Add Category";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $slug = strtolower(str_replace(' ', '-', trim($name)));

    // Check if slug already exists
    $checkSlug = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
    $checkSlug->execute([$slug]);
    $slugExists = $checkSlug->fetchColumn();

    if ($slugExists) {
        $error = "A category with this name already exists. Please choose a different name.";
    } else {
        try {
            $query = "INSERT INTO categories (name, description, parent_id, slug) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $description, $parent_id, $slug]);
            header('Location: categories.php?success=Category added successfully');
            exit;
        } catch (PDOException $e) {
            $error = "Error adding category: " . $e->getMessage();
        }
    }
}

?>

<div class="container mt-4">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-header">
            <h2>Add New Category</h2>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        <option value="">None</option>
                        <!-- Populate with categories -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
</div>

<?php 
require_once 'includes/admin_footer.php';
ob_end_flush(); // End output buffering
?>