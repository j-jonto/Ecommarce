<?php
$pageTitle = "Dashboard";
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../includes/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get counts for dashboard
$query = "SELECT COUNT(*) as count FROM products";
$stmt = $pdo->prepare($query);
$stmt->execute();
$productCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$query = "SELECT COUNT(*) as count FROM categories";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categoryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$query = "SELECT COUNT(*) as count FROM orders";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orderCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$query = "SELECT COUNT(*) as count FROM customers";
$stmt = $pdo->prepare($query);
$stmt->execute();
$customerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get recent orders
$query = "SELECT o.*, c.name as customer_name 
          FROM orders o 
          JOIN customers c ON o.customer_id = c.id 
          ORDER BY o.order_date DESC 
          LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute();
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-title">Total Products</div>
        <div class="card-value"><?php echo $productCount; ?></div>
        <a href="products.php" class="card-link">View All</a>
    </div>

    <div class="dashboard-card">
        <div class="card-title">Total Categories</div>
        <div class="card-value"><?php echo $categoryCount; ?></div>
        <a href="categories.php" class="card-link">View All</a>
    </div>

    <div class="dashboard-card">
        <div class="card-title">Total Orders</div>
        <div class="card-value"><?php echo $orderCount; ?></div>
        <a href="orders.php" class="card-link">View All</a>
    </div>

    <div class="dashboard-card">
        <div class="card-title">Total Customers</div>
        <div class="card-value"><?php echo $customerCount; ?></div>
    </div>
</div>

<div class="recent-orders">
    <h2>Recent Orders</h2>

    <?php if (count($recentOrders) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm">View</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No recent orders found.</p>
    <?php endif; ?>
</div>

<style>
.dashboard-card {
    display: flex;
    flex-direction: column;
}

.card-link {
    margin-top: 10px;
    font-size: 0.9rem;
}

.recent-orders {
    margin-top: 40px;
}

.recent-orders h2 {
    margin-bottom: 20px;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
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