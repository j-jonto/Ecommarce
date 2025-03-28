<?php
$pageTitle = "Admin Login";
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    if (!headers_sent()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        // Verify login credentials
        if (verifyAdminLogin($pdo, $username, $password)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/normalize.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/admin/assets/css/admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="login-container">
            <div class="login-logo">
                <h1><?php echo SITE_NAME; ?> Admin</h1>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="" class="login-form validated-form">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="login-footer">
                <a href="<?php echo SITE_URL; ?>">Back to Site</a>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/admin/assets/js/admin-scripts.js"></script>
</body>
</html>