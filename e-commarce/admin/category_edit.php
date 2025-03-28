<?php
$pageTitle = "Edit Category";
require_once 'includes/admin_header.php';

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$categoryId = (int)$_GET['id'];

// Get category details
$query = "SELECT * FROM categories WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $categoryId);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if category exists
if (!$category) {
    header("Location: categories.php");
    exit;
}

// Get all parent categories for dropdown (excluding the current category and its children)
$query = "SELECT id, name FROM categories 
          WHERE id != :current_id 
          AND parent_id IS NULL 
          AND id NOT IN (SELECT id FROM categories WHERE parent_id = :current_id)
          ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':current_id', $categoryId);
$stmt->execute();
$parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $parentId = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    
    // Generate slug from name
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]+/', '-', $name), '-'));
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }
    
    // Check if slug already exists (excluding current category)
    $query = "SELECT COUNT(*) as count FROM categories WHERE slug = :slug AND id != :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':id', $categoryId);
    $stmt->execute();
    
    if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
        $errors[] = "A category with this name/slug already exists.";
    }
    
    if (empty($errors)) {
        try {
            // Update category
            $query = "UPDATE categories 
                      SET name = :name, slug = :slug, description = :description, parent_id = :parent_id
                      WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':parent_id', $parentId);
            $stmt->bindParam(':id', $categoryId);
            $stmt->execute();
            
            $success = "Category updated successfully.";
            
            // Redirect to categories list
            header("Location: categories.php?success=" . urlencode($success));
            exit;
        } catch (PDOException $e) {
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Edit Category</h1>
    <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="" class="validated-form">
    <div class="form-group">
        <label for="name" class="form-label">Category Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="parent_id" class="form-label">Parent Category (optional)</label>
        <select id="parent_id" name="parent_id" class="form-control">
            <option value="">None (Main Category)</option>
            <?php foreach ($parentCategories as $parent): ?>
                <option value="<?php echo $parent['id']; ?>" <?php echo $category['parent_id'] == $parent['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($parent['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="description" class="form-label">Description (optional)</label>
        <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-success">Update Category</button>
</form>

<?php require_once 'includes/admin_footer.php'; ?>