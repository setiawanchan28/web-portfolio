<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helper.php';

if (is_logged_in()) {
    header("Location: " . ADMIN_URL);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi!';
    } else {
        $stmt = db()->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ADMIN_URL);
            exit;
        } else {
            $error = 'Username atau Password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Futuristic Portfolio CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/admin.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at center, #0f172a 0%, #080c14 100%);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 16px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-brand i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 12px;
        }
        .login-brand h4 {
            color: var(--text-main);
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="glass-card login-card">
        <div class="login-brand">
            <i class="fas fa-user-shield"></i>
            <h4>Admin Control Panel</h4>
            <p class="text-muted small">Sign in to manage portfolio content</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-2 fw-semibold"><i class="fas fa-sign-in-alt me-2"></i> Login Dashboard</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Kembali ke Website Main</a>
        </div>
    </div>

</body>
</html>
