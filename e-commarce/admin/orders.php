<?php
$pageTitle = "Manage Orders";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get all orders with customer info
$query = "SELECT o.id, o.order_date, o.total_amount, o.status, c.name as customer_name, c.email as customer_email
          FROM orders o
          JOIN customers c ON o.customer_id = c.id
          ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Manage Orders</h1>
</div>

<?php if (count($orders) > 0): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td>
                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No orders found.</p>
<?php endif; ?>

<style>
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