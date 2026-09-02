<?php
// Script to generate fresh BCRYPT hash for password: admin123
$pass = 'admin123';
$hash = password_hash($pass, PASSWORD_DEFAULT);
echo "New Hash: " . $hash;
