<?php
// SQLite database setup script

// Include the database class
require_once __DIR__ . '/../includes/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

try {
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database_sqlite.sql');

    // Split into individual statements
    $statements = explode(';', $sql);

    // Execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "Database schema created successfully.<br>";

    // Insert default admin user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admin_users (username, password, email) 
                    VALUES ('admin', '$hash', 'admin@example.com')");
        echo "Default admin user created.<br>";
    }

    echo "<p>Setup completed successfully! <a href='index.php'>Go to website</a></p>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>