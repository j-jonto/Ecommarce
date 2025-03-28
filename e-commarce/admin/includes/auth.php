<?php
require_once __DIR__ . '/../../includes/database.php';

function verifyAdminLogin($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . SITE_URL . "/admin/index.php");
        exit();
    }
}

function logoutAdmin() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    session_destroy();
    header("Location: " . SITE_URL . "/admin/index.php"); // Added for consistency
    exit();
}

/**
 * Change admin password
 * @param PDO $pdo Database connection
 * @param int $adminId Admin ID
 * @param string $newPassword New password
 * @return bool Success status
 */
function changeAdminPassword($pdo, $adminId, $newPassword) {
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $query = "UPDATE admin_users SET password = :password WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':password', $passwordHash);
    $stmt->bindParam(':id', $adminId);
    
    return $stmt->execute();
}
?>