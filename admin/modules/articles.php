<?php
$page_title = 'Kelola Articles / Blog';
$active_menu = 'articles';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    db()->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    set_flash('success', 'Artikel berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/articles.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $slug = create_slug($title);
    $category_id = $_POST['category_id'] ?? null;
    $excerpt = $_POST['excerpt'] ?? '';
    $content = $_POST['content'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $reading_time = $_POST['reading_time'] ?? 5;
    $status = $_POST['status'] ?? 'published';

    $thumbnail = $_POST['existing_thumbnail'] ?? null;
    $uploaded_thumb = upload_file('thumbnail', 'articles');
    if ($uploaded_thumb) {
        $thumbnail = $uploaded_thumb;
    }

    if ($id > 0) {
        $stmt = db()->prepare("UPDATE articles SET category_id=?, title=?, slug=?, excerpt=?, content=?, thumbnail=?, tags=?, reading_time=?, status=? WHERE id=?");
        $stmt->execute([$category_id, $title, $slug, $excerpt, $content, $thumbnail, $tags, $reading_time, $status, $id]);
        set_flash('success', 'Artikel berhasil diperbarui!');
    } else {
        $stmt = db()->prepare("INSERT INTO articles (category_id, title, slug, excerpt, content, thumbnail, tags, reading_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $title, $slug, $excerpt, $content, $thumbnail, $tags, $reading_time, $status]);
        set_flash('success', 'Artikel baru berhasil dipublikasi!');
    }

    header("Location: " . ADMIN_URL . "modules/articles.php");
    exit;
}

if ($action === 'add' || $action === 'edit'):
    $article = null;
    if ($action === 'edit') {
        $id = $_GET['id'] ?? 0;
        $stmt = db()->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
    }
    $categories = db()->query("SELECT * FROM article_categories ORDER BY name ASC")->fetchAll();
?>
    <div class="glass-card p-4">
        <h5 class="text-info mb-4"><?= $action === 'edit' ? 'Edit Artikel' : 'Tulis Artikel Baru' ?></h5>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $article['id'] ?? 0 ?>">
            <input type="hidden" name="existing_thumbnail" value="<?= sanitize($article['thumbnail'] ?? '') ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Judul Artikel</label>
                    <input type="text" name="title" class="form-control" value="<?= sanitize($article['title'] ?? '') ?>" required placeholder="Judul artikel teknologi...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Tanpa Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Excerpt / Ringkasan</label>
                    <textarea name="excerpt" class="form-control" rows="2" placeholder="Ringkasan singkat artikel..."><?= sanitize($article['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Konten Lengkap Artikel</label>
                    <textarea name="content" class="form-control" rows="10" required><?= sanitize($article['content'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tags (Pisahkan koma)</label>
                    <input type="text" name="tags" class="form-control" value="<?= sanitize($article['tags'] ?? '') ?>" placeholder="PHP, Web, Tutorials">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reading Time (Menit)</label>
                    <input type="number" name="reading_time" class="form-control" value="<?= $article['reading_time'] ?? 5 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= ($article['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Thumbnail Artikel</label>
                    <?php if (!empty($article['thumbnail'])): ?>
                        <div class="mb-2"><img src="<?= UPLOAD_URL . sanitize($article['thumbnail']) ?>" style="max-height: 100px; border-radius: 6px;"></div>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-info px-4 fw-semibold"><i class="fas fa-paper-plane me-2"></i> Simpan & Dipublikasikan</button>
                <a href="<?= ADMIN_URL ?>modules/articles.php" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        </form>
    </div>
<?php else: 
    $articles = db()->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN article_categories c ON a.category_id = c.id ORDER BY a.id DESC")->fetchAll();
?>
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 text-info"><i class="fas fa-newspaper me-2"></i>Daftar Artikel & Blog</h5>
            <a href="<?= ADMIN_URL ?>modules/articles.php?action=add" class="btn btn-info btn-sm fw-semibold"><i class="fas fa-pen me-1"></i> Tulis Artikel Baru</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada artikel. Tulis artikel pertama Anda!</td></tr>
                    <?php else: ?>
                        <?php foreach ($articles as $art): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($art['thumbnail'])): ?>
                                        <img src="<?= UPLOAD_URL . sanitize($art['thumbnail']) ?>" style="width: 45px; height: 35px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Pic</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= sanitize($art['title']) ?></strong></td>
                                <td><small class="text-info"><?= sanitize($art['category_name'] ?? 'Umum') ?></small></td>
                                <td>
                                    <span class="badge <?= $art['status'] === 'published' ? 'bg-success' : 'bg-warning' ?>"><?= ucfirst($art['status']) ?></span>
                                </td>
                                <td><small class="text-muted"><?= date('d M Y', strtotime($art['created_at'])) ?></small></td>
                                <td>
                                    <a href="<?= ADMIN_URL ?>modules/articles.php?action=edit&id=<?= $art['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-edit"></i></a>
                                    <a href="<?= ADMIN_URL ?>modules/articles.php?action=delete&id=<?= $art['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus artikel ini?')"><i class="fas fa-trash"></i></a>
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
