<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';
require_login();

$user_id = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($username)) {
        set_flash('danger', 'Nama Lengkap, Email, dan Username tidak boleh kosong!');
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        set_flash('danger', 'Konfirmasi password baru tidak cocok!');
    } else {
        if (!empty($new_password)) {
            $hashed_pass = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = db()->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, password = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $email, $username, $hashed_pass, $user_id]);
        } else {
            $update_stmt = db()->prepare("UPDATE users SET full_name = ?, email = ?, username = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $email, $username, $user_id]);
        }

        $_SESSION['username'] = $username;
        set_flash('success', 'Akun Administrator berhasil diperbarui!');
        header("Location: " . ADMIN_URL . "modules/account.php");
        exit;
    }
}

$page_title = 'Edit Akun Administrator';
$active_menu = 'account';
require_once __DIR__ . '/../includes/header.php';

// Fetch active admin user details
$stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();
?>

<div class="glass-card p-4" style="max-width: 700px;">
    <h5 class="text-info mb-4"><i class="fas fa-user-cog me-2"></i>Pengaturan Akun & Password Admin</h5>

    <form action="" method="POST">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Nama Lengkap Administrator</label>
                <input type="text" name="full_name" class="form-control" value="<?= sanitize($admin['full_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Administrator</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($admin['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Username Login</label>
                <input type="text" name="username" class="form-control" value="<?= sanitize($admin['username'] ?? '') ?>" required>
            </div>

            <hr class="border-secondary mt-4">
            <h6 class="text-white mb-2"><i class="fas fa-lock me-2 text-warning"></i>Ganti Password (Opsional)</h6>
            <small class="text-muted mb-3 d-block">Kosongkan jika tidak ingin mengubah password saat ini.</small>

            <div class="col-md-6">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru">
            </div>
            <div class="col-md-6">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Perubahan Akun</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
