<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';

$page_title = 'Kelola Messages Masuk';
$active_menu = 'messages';

$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    $stmt = db()->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    set_flash('success', 'Pesan berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/messages.php");
    exit;
}

if ($action === 'view') {
    $id = $_GET['id'] ?? 0;
    // Mark as read
    db()->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    $msg_stmt = db()->prepare("SELECT * FROM messages WHERE id = ?");
    $msg_stmt->execute([$id]);
    $message = $msg_stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($action === 'view' && $message): ?>
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 text-info"><i class="fas fa-envelope-open me-2"></i>Detail Pesan Masuk</h5>
            <a href="<?= ADMIN_URL ?>modules/messages.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
        <div class="mb-3">
            <strong>Dari:</strong> <?= sanitize($message['name']) ?> (&lt;<a href="mailto:<?= sanitize($message['email']) ?>" class="text-info"><?= sanitize($message['email']) ?></a>&gt;)<br>
            <?php if (!empty($message['phone'])): 
                $clean_phone = preg_replace('/[^0-9]/', '', $message['phone']);
                if (str_starts_with($clean_phone, '0')) {
                    $clean_phone = '62' . substr($clean_phone, 1);
                }
            ?>
                <strong>No. HP / WhatsApp:</strong> <a href="https://wa.me/<?= $clean_phone ?>" target="_blank" class="text-success fw-semibold"><i class="fab fa-whatsapp me-1"></i><?= sanitize($message['phone']) ?></a><br>
            <?php endif; ?>
            <strong>Tanggal:</strong> <?= date('d F Y H:i', strtotime($message['created_at'])) ?><br>
            <strong>Subjek:</strong> <?= sanitize($message['subject']) ?>
        </div>
        <hr class="border-secondary">
        <div class="p-3 bg-dark rounded text-light mb-4" style="white-space: pre-wrap; font-size: 1.05rem;">
            <?= sanitize($message['message']) ?>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="mailto:<?= sanitize($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" class="btn btn-info fw-semibold"><i class="fas fa-reply me-1"></i> Balas Via Email</a>
            <?php if (!empty($message['phone'])): ?>
                <a href="https://wa.me/<?= $clean_phone ?>?text=Halo%20<?= urlencode($message['name']) ?>," target="_blank" class="btn btn-success fw-semibold"><i class="fab fa-whatsapp me-1"></i> Balas Via WhatsApp</a>
            <?php endif; ?>
            <a href="<?= ADMIN_URL ?>modules/messages.php?action=delete&id=<?= $message['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus pesan ini?')"><i class="fas fa-trash me-1"></i> Hapus Pesan</a>
        </div>
    </div>
<?php else: 
    $messages = db()->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();
?>
    <div class="glass-card p-4">
        <h5 class="text-info mb-4"><i class="fas fa-inbox me-2"></i>Daftar Pesan Kontak Form</h5>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Pengirim</th>
                        <th>Email</th>
                        <th>No. HP / WA</th>
                        <th>Subject</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada pesan masuk.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td><strong><?= sanitize($m['name']) ?></strong></td>
                                <td><a href="mailto:<?= sanitize($m['email']) ?>" class="text-info"><?= sanitize($m['email']) ?></a></td>
                                <td><?= !empty($m['phone']) ? sanitize($m['phone']) : '<span class="text-muted">-</span>' ?></td>
                                <td><?= sanitize($m['subject']) ?></td>
                                <td><small class="text-muted"><?= date('d M Y H:i', strtotime($m['created_at'])) ?></small></td>
                                <td>
                                    <?php if ($m['is_read']): ?>
                                        <span class="badge bg-secondary">Dibaca</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Baru</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>modules/messages.php?action=view&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i> Baca</a>
                                    <a href="<?= ADMIN_URL ?>modules/messages.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pesan ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
