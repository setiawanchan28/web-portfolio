<?php
$page_title = 'Kelola Pengalaman Kerja';
$active_menu = 'experiences';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    $stmt = db()->prepare("DELETE FROM experiences WHERE id = ?");
    $stmt->execute([$id]);
    set_flash('success', 'Data pengalaman berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/experiences.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $position = $_POST['position'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $is_current = isset($_POST['is_current']) ? 1 : 0;
    $description = $_POST['description'] ?? '';
    $tech_used = $_POST['tech_used'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    if ($id > 0) {
        $stmt = db()->prepare("UPDATE experiences SET position=?, company=?, location=?, start_date=?, end_date=?, is_current=?, description=?, tech_used=?, display_order=? WHERE id=?");
        $stmt->execute([$position, $company, $location, $start_date, $end_date, $is_current, $description, $tech_used, $display_order, $id]);
        set_flash('success', 'Pengalaman berhasil diperbarui!');
    } else {
        $stmt = db()->prepare("INSERT INTO experiences (position, company, location, start_date, end_date, is_current, description, tech_used, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$position, $company, $location, $start_date, $end_date, $is_current, $description, $tech_used, $display_order]);
        set_flash('success', 'Pengalaman baru berhasil ditambahkan!');
    }

    header("Location: " . ADMIN_URL . "modules/experiences.php");
    exit;
}

if ($action === 'add' || $action === 'edit'):
    $exp = null;
    if ($action === 'edit') {
        $id = $_GET['id'] ?? 0;
        $stmt = db()->prepare("SELECT * FROM experiences WHERE id = ?");
        $stmt->execute([$id]);
        $exp = $stmt->fetch();
    }
?>
    <div class="glass-card p-4">
        <h5 class="text-info mb-4"><?= $action === 'edit' ? 'Edit Pengalaman' : 'Tambah Pengalaman Baru' ?></h5>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $exp['id'] ?? 0 ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Posisi / Jabatan</label>
                    <input type="text" name="position" class="form-control" value="<?= sanitize($exp['position'] ?? '') ?>" required placeholder="Contoh: Senior Fullstack Developer / Pranata Komputer">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Perusahaan / Instansi</label>
                    <input type="text" name="company" class="form-control" value="<?= sanitize($exp['company'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location" class="form-control" value="<?= sanitize($exp['location'] ?? '') ?>" placeholder="Jakarta, Indonesia">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun / Tanggal Mulai</label>
                    <input type="text" name="start_date" class="form-control" value="<?= sanitize($exp['start_date'] ?? '') ?>" required placeholder="Jan 2022">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun / Tanggal Selesai</label>
                    <input type="text" name="end_date" class="form-control" value="<?= sanitize($exp['end_date'] ?? '') ?>" placeholder="Present / Des 2024">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_current" id="is_current" <?= !empty($exp['is_current']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-white" for="is_current">Saat ini masih bekerja di posisi ini</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Pekerjaan & Achievements</label>
                    <textarea name="description" class="form-control" rows="4"><?= sanitize($exp['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Teknologi yang Digunakan</label>
                    <input type="text" name="tech_used" class="form-control" value="<?= sanitize($exp['tech_used'] ?? '') ?>" placeholder="PHP, Laravel, MySQL, Docker">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="display_order" class="form-control" value="<?= $exp['display_order'] ?? 0 ?>">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-save me-2"></i> Simpan Data</button>
                <a href="<?= ADMIN_URL ?>modules/experiences.php" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        </form>
    </div>
<?php else: 
    $experiences = db()->query("SELECT * FROM experiences ORDER BY display_order ASC, id DESC")->fetchAll();
?>
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 text-info"><i class="fas fa-briefcase me-2"></i>Timeline Pengalaman Kerja</h5>
            <a href="<?= ADMIN_URL ?>modules/experiences.php?action=add" class="btn btn-info btn-sm fw-semibold"><i class="fas fa-plus me-1"></i> Tambah Pengalaman</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Periode</th>
                        <th>Teknologi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($experiences)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada data pengalaman kerja.</td></tr>
                    <?php else: ?>
                        <?php foreach ($experiences as $ex): ?>
                            <tr>
                                <td><strong><?= sanitize($ex['position']) ?></strong></td>
                                <td><?= sanitize($ex['company']) ?></td>
                                <td><small class="text-muted"><?= sanitize($ex['start_date']) ?> - <?= $ex['is_current'] ? 'Present' : sanitize($ex['end_date']) ?></small></td>
                                <td><small class="text-info"><?= sanitize($ex['tech_used']) ?></small></td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>modules/experiences.php?action=edit&id=<?= $ex['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a>
                                    <a href="<?= ADMIN_URL ?>modules/experiences.php?action=delete&id=<?= $ex['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
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
