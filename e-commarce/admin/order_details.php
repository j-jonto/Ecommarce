<?php
$pageTitle = "Order Details";
require_once 'includes/admin_header.php';

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$orderId = (int)$_GET['id'];
$successMessage = '';
$errorMessage = '';

// Update order status if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = sanitize($_POST['status']);
    
    try {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        
        $successMessage = "Order status updated successfully.";
    } catch (PDOException $e) {
        $errorMessage = "An error occurred while updating the order status.";
        
        if (DEBUG_MODE) {
            $errorMessage .= " " . $e->getMessage();
        }
    }
}

// Get order details
$query = "SELECT o.*, c.name, c.email, c.phone, c.address
          FROM orders o
          JOIN customers c ON o.customer_id = c.id
          WHERE o.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $orderId);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Get order items
$query = "SELECT oi.*, p.name, p.image_path
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':order_id', $orderId);
$stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h1>
    <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
</div>

<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<div class="order-details">
    <div class="order-info-container">
        <div class="order-info-card">
            <h2>Order Information</h2>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value"><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Order Total:</span>
                    <span class="info-value"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                </div>
            </div>
            
            <form method="post" action="" class="status-form">
                <div class="form-group">
                    <label for="status" class="form-label">Update Status:</label>
                    <select id="status" name="status" class="form-control">
                        <option value="new" <?php echo $order['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
        
        <div class="order-info-card">
            <h2>Customer Information</h2>
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value"><?php echo nl2br(htmlspecialchars($order['address'])); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="order-items-section">
        <h2>Order Items</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <div class="order-product">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-product-image">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price_at_time_of_order']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatPrice($item['price_at_time_of_order'] * $item['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total-label">Total</td>
                    <td class="total-value"><?php echo formatPrice($order['total_amount']); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
.order-info-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 40px;
}

.order-info-card {
    flex: 1;
    min-width: 300px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.order-info-card h2 {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
}

.info-list {
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    margin-bottom: 10px;
}

.info-label {
    font-weight: 600;
    width: 100px;
}

.info-value {
    flex: 1;
}

.status-form {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.order-items-section {
    margin-top: 30px;
}

.order-items-section h2 {
    margin-bottom: 20px;
}

.order-product {
    display: flex;
    align-items: center;
}

.order-product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    margin-right: 10px;
    border-radius: 4px;
}

.total-label {
    text-align: right;
    font-weight: 600;
}

.total-value {
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-new {
    background-color: #cce5ff;
    color: #004085;
}

.status-processing {
    background-color: #fff3cd;
    color: #856404;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>