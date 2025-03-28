<?php
$pageTitle = "Categories";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get all categories with their parent names
$query = "SELECT c.*, p.name as parent_name
          FROM categories c
          LEFT JOIN categories p ON c.parent_id = p.id
          ORDER BY c.name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $categoryId = (int)$_POST['category_id'];

    try {
        // Check if category has products
        $query = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        $productCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($productCount > 0) {
            $error = "Cannot delete category: it contains $productCount product(s). Please move or delete these products first.";
        } else {
            // Delete category
            $query = "DELETE FROM categories WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $categoryId);
            $stmt->execute();

            $success = "Category deleted successfully.";

            // Redirect to refresh the page
            header("Location: categories.php?success=" . urlencode($success));
            exit;
        }
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
    <h1 class="page-title">Manage Categories</h1>
    <a href="category_add.php" class="btn">Add New Category</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (count($categories) > 0): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Parent Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                    <td>
                        <?php if ($category['parent_id']): ?>
                            <?php echo htmlspecialchars($category['parent_name']); ?>
                        <?php else: ?>
                            <em>None (Main Category)</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="category_edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm">Edit</a>

                            <form method="post" action="" style="display: inline;">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="delete_category" class="btn btn-sm btn-danger delete-btn">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No categories found. <a href="category_add.php">Add a category</a> to get started.</p>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>