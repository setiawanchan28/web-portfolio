<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = db()->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
$stmt->execute(['pass' => $new_hash]);

echo "Successfully updated password to admin123. Hash: " . $new_hash;
