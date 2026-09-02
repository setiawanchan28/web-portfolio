<?php
$page_title = 'Kelola Education & Certifications';
$active_menu = 'educations';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

$action = $_GET['action'] ?? 'list';

if ($action === 'delete_edu') {
    $id = $_GET['id'] ?? 0;
    db()->prepare("DELETE FROM educations WHERE id = ?")->execute([$id]);
    set_flash('success', 'Data pendidikan berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/educations.php");
    exit;
}

if ($action === 'delete_cert') {
    $id = $_GET['id'] ?? 0;
    db()->prepare("DELETE FROM certificates WHERE id = ?")->execute([$id]);
    set_flash('success', 'Data sertifikat berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/educations.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_edu'])) {
    $institution = $_POST['institution'] ?? '';
    $degree_major = $_POST['degree_major'] ?? '';
    $location = $_POST['location'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $description = $_POST['description'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    $stmt = db()->prepare("INSERT INTO educations (institution, degree_major, location, start_year, end_year, description, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$institution, $degree_major, $location, $start_year, $end_year, $description, $display_order]);
    set_flash('success', 'Data Pendidikan berhasil ditambahkan!');
    header("Location: " . ADMIN_URL . "modules/educations.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cert'])) {
    $title = $_POST['title'] ?? '';
    $issuer = $_POST['issuer'] ?? '';
    $issue_year = $_POST['issue_year'] ?? '';
    $credential_id = $_POST['credential_id'] ?? '';
    $credential_url = $_POST['credential_url'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    $image_path = upload_file('image_path', 'certificates');

    $stmt = db()->prepare("INSERT INTO certificates (title, issuer, issue_year, credential_id, credential_url, image_path, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $issuer, $issue_year, $credential_id, $credential_url, $image_path, $display_order]);
    set_flash('success', 'Sertifikat baru berhasil ditambahkan!');
    header("Location: " . ADMIN_URL . "modules/educations.php");
    exit;
}

$educations = db()->query("SELECT * FROM educations ORDER BY display_order ASC, id DESC")->fetchAll();
$certificates = db()->query("SELECT * FROM certificates ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="row g-4 mb-4">
    <!-- Education Form -->
    <div class="col-md-6">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-graduation-cap me-2"></i>Tambah Pendidikan</h5>
            <form action="" method="POST">
                <input type="hidden" name="save_edu" value="1">
                <div class="mb-3">
                    <label class="form-label">Institusi / Universitas</label>
                    <input type="text" name="institution" class="form-control" required placeholder="Universitas / Politeknik">
                </div>
                <div class="mb-3">
                    <label class="form-label">Program Studi / Gelar</label>
                    <input type="text" name="degree_major" class="form-control" required placeholder="S1 Teknik Informatika">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Tahun Mulai</label>
                        <input type="text" name="start_year" class="form-control" placeholder="2018">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Tahun Selesai</label>
                        <input type="text" name="end_year" class="form-control" placeholder="2022">
                    </div>
                </div>
                <button type="submit" class="btn btn-info w-100 fw-semibold"><i class="fas fa-plus me-1"></i> Simpan Pendidikan</button>
            </form>
        </div>
    </div>

    <!-- Certificate Form -->
    <div class="col-md-6">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-certificate me-2"></i>Tambah Sertifikat</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="save_cert" value="1">
                <div class="mb-3">
                    <label class="form-label">Nama Sertifikat</label>
                    <input type="text" name="title" class="form-control" required placeholder="AWS Certified Solutions Architect">
                </div>
                <div class="mb-3">
                    <label class="form-label">Penerbit / Publisher</label>
                    <input type="text" name="issuer" class="form-control" required placeholder="Amazon Web Services / Coursera">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="text" name="issue_year" class="form-control" placeholder="2024">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Credential ID</label>
                        <input type="text" name="credential_id" class="form-control" placeholder="ABC-12345">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gambar Sertifikat</label>
                    <input type="file" name="image_path" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-info w-100 fw-semibold"><i class="fas fa-plus me-1"></i> Simpan Sertifikat</button>
            </form>
        </div>
    </div>
</div>

<!-- Education List -->
<div class="glass-card p-4 mb-4">
    <h5 class="text-info mb-3"><i class="fas fa-list me-2"></i>Daftar Pendidikan</h5>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Institusi</th>
                    <th>Jurusan</th>
                    <th>Tahun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($educations)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada data pendidikan.</td></tr>
                <?php else: ?>
                    <?php foreach ($educations as $edu): ?>
                        <tr>
                            <td><strong><?= sanitize($edu['institution']) ?></strong></td>
                            <td><?= sanitize($edu['degree_major']) ?></td>
                            <td><small class="text-muted"><?= sanitize($edu['start_year']) ?> - <?= sanitize($edu['end_year']) ?></small></td>
                            <td>
                                <a href="<?= ADMIN_URL ?>modules/educations.php?action=delete_edu&id=<?= $edu['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data pendidikan ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Certificates List -->
<div class="glass-card p-4">
    <h5 class="text-info mb-3"><i class="fas fa-award me-2"></i>Daftar Sertifikat</h5>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Judul Sertifikat</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($certificates)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Belum ada sertifikat.</td></tr>
                <?php else: ?>
                    <?php foreach ($certificates as $cert): ?>
                        <tr>
                            <td>
                                <?php if (!empty($cert['image_path'])): ?>
                                    <img src="<?= UPLOAD_URL . sanitize($cert['image_path']) ?>" style="height: 40px; border-radius: 4px;">
                                <?php else: ?>
                                    <span class="badge bg-secondary">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= sanitize($cert['title']) ?></strong></td>
                            <td><?= sanitize($cert['issuer']) ?></td>
                            <td><small class="text-muted"><?= sanitize($cert['issue_year']) ?></small></td>
                            <td>
                                <a href="<?= ADMIN_URL ?>modules/educations.php?action=delete_cert&id=<?= $cert['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus sertifikat ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
