<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';

$page_title = 'Kelola Skills & Kategori';
$active_menu = 'skills';

// Handle Action
$action = $_GET['action'] ?? 'list';

// Handle Delete Skill Category
if ($action === 'delete_cat') {
    $cat_id = $_GET['id'] ?? 0;
    $stmt = db()->prepare("DELETE FROM skill_categories WHERE id = ?");
    $stmt->execute([$cat_id]);
    set_flash('success', 'Kategori skill berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

// Handle Delete Skill Item
if ($action === 'delete_skill') {
    $skill_id = $_GET['id'] ?? 0;
    $stmt = db()->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$skill_id]);
    set_flash('success', 'Skill item berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

// Handle Save Skill Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $cat_name = $_POST['category_name'] ?? '';
    $cat_name_en = $_POST['category_name_en'] ?? '';
    $order = $_POST['display_order'] ?? 0;

    if (!empty($cat_name)) {
        $stmt = db()->prepare("INSERT INTO skill_categories (category_name, category_name_en, display_order) VALUES (?, ?, ?)");
        $stmt->execute([$cat_name, $cat_name_en, $order]);
        set_flash('success', 'Kategori baru berhasil ditambahkan!');
    }
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

// Handle Update Skill Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $cat_id = $_POST['category_id'] ?? 0;
    $cat_name = $_POST['category_name'] ?? '';
    $cat_name_en = $_POST['category_name_en'] ?? '';
    $order = $_POST['display_order'] ?? 0;

    if ($cat_id > 0 && !empty($cat_name)) {
        $stmt = db()->prepare("UPDATE skill_categories SET category_name = ?, category_name_en = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$cat_name, $cat_name_en, $order, $cat_id]);
        set_flash('success', 'Kategori skill berhasil diperbarui!');
    }
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

// Handle Save Skill Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_skill'])) {
    $cat_id = $_POST['category_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $level = $_POST['level_percentage'] ?? 85;
    $icon = $_POST['icon_class'] ?? 'fas fa-check-circle';
    $order = $_POST['display_order'] ?? 0;

    if (!empty($name) && $cat_id > 0) {
        $stmt = db()->prepare("INSERT INTO skills (category_id, name, name_en, level_percentage, icon_class, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cat_id, $name, $name_en, $level, $icon, $order]);
        set_flash('success', 'Skill baru berhasil ditambahkan!');
    }
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

// Handle Update Skill Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skill'])) {
    $skill_id = $_POST['skill_id'] ?? 0;
    $cat_id = $_POST['category_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $name_en = $_POST['name_en'] ?? '';
    $icon = $_POST['icon_class'] ?? 'fas fa-check-circle';
    $order = $_POST['display_order'] ?? 0;

    if ($skill_id > 0 && !empty($name) && $cat_id > 0) {
        $stmt = db()->prepare("UPDATE skills SET category_id = ?, name = ?, name_en = ?, icon_class = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$cat_id, $name, $name_en, $icon, $order, $skill_id]);
        set_flash('success', 'Skill item berhasil diperbarui!');
    }
    header("Location: " . ADMIN_URL . "modules/skills.php");
    exit;
}

require_once __DIR__ . '/../includes/header.php';

// Fetch all categories & skills
$categories = db()->query("SELECT * FROM skill_categories ORDER BY display_order ASC")->fetchAll();
?>

<div class="row g-4 mb-4">
    <!-- Add Category Form -->
    <div class="col-md-5">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-folder-plus me-2"></i>Tambah Kategori Skill</h5>
            <form action="" method="POST">
                <input type="hidden" name="save_category" value="1">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="category_name" class="form-control" placeholder="Contoh: Frontend, Backend, Tools" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="display_order" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-info w-100 fw-semibold"><i class="fas fa-plus me-1"></i> Simpan Kategori</button>
            </form>
        </div>
    </div>

    <!-- Add Skill Form -->
    <div class="col-md-7">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-code me-2"></i>Tambah Item Skill</h5>
            <form action="" method="POST">
                <input type="hidden" name="save_skill" value="1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Pilih Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= sanitize($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Skill (ID)</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Network Maintenance" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-info"><i class="fas fa-language me-1"></i>Nama Skill (EN)</label>
                        <input type="text" name="name_en" class="form-control" placeholder="e.g. Network Maintenance">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Pilih Ikon Visual</label>
                        <input type="hidden" name="icon_class" id="icon_class_input" value="fas fa-check-circle">
                        <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                            <span class="d-flex align-items-center">
                                <i id="selected_icon_preview" class="fas fa-check-circle text-info me-2 fs-5"></i>
                                <span id="selected_icon_name" class="fw-semibold">Centang / Standar</span>
                            </span>
                            <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-info w-100 fw-semibold py-2"><i class="fas fa-plus me-1"></i> Simpan Skill Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category & Skills List -->
<div class="glass-card p-4">
    <h5 class="text-info mb-3"><i class="fas fa-list me-2"></i>Daftar Skill Per Kategori</h5>
    <?php if (empty($categories)): ?>
        <div class="text-muted text-center py-4">Belum ada kategori skill. Tambahkan kategori terlebih dahulu.</div>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <div class="border border-secondary border-opacity-25 rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="m-0 text-white font-weight-bold"><i class="fas fa-layer-group text-info me-2"></i><?= sanitize($cat['category_name']) ?> <small class="text-muted">(Urutan: <?= $cat['display_order'] ?>)</small></h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-info edit-cat-btn me-2" 
                                data-id="<?= $cat['id'] ?>" 
                                data-name="<?= sanitize($cat['category_name']) ?>" 
                                data-order="<?= $cat['display_order'] ?>">
                            <i class="fas fa-edit me-1"></i> Edit Kategori
                        </button>
                        <a href="<?= ADMIN_URL ?>modules/skills.php?action=delete_cat&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori beserta seluruh skill di dalamnya?')">
                            <i class="fas fa-trash me-1"></i> Hapus Kategori
                        </a>
                    </div>
                </div>
                
                <?php
                $skills_stmt = db()->prepare("SELECT * FROM skills WHERE category_id = ? ORDER BY display_order ASC");
                $skills_stmt->execute([$cat['id']]);
                $skills = $skills_stmt->fetchAll();
                ?>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Ikon</th>
                                <th>Nama Skill / Kemampuan</th>
                                <th>Urutan</th>
                                <th style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($skills)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada skill dalam kategori ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($skills as $skill): ?>
                                    <tr>
                                        <td>
                                            <div class="bg-dark p-2 rounded text-center border border-secondary border-opacity-25">
                                                <i class="<?= sanitize(!empty($skill['icon_class']) ? $skill['icon_class'] : 'fas fa-check-circle') ?> fs-5 text-info"></i>
                                            </div>
                                        </td>
                                        <td><strong class="fs-6"><?= sanitize($skill['name']) ?></strong></td>
                                        <td><?= $skill['display_order'] ?></td>
                                        <td style="white-space: nowrap;">
                                            <button type="button" class="btn btn-sm btn-outline-info edit-skill-btn me-1" 
                                                    data-id="<?= $skill['id'] ?>" 
                                                    data-catid="<?= $skill['category_id'] ?>" 
                                                    data-name="<?= sanitize($skill['name']) ?>" 
                                                    data-icon="<?= sanitize($skill['icon_class'] ?? 'fas fa-check-circle') ?>" 
                                                    data-order="<?= $skill['display_order'] ?>">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>
                                            <a href="<?= ADMIN_URL ?>modules/skills.php?action=delete_skill&id=<?= $skill['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus skill ini?')">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Edit Skill Category -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-info text-light">
            <form action="" method="POST">
                <input type="hidden" name="update_category" value="1">
                <input type="hidden" name="category_id" id="edit_cat_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info"><i class="fas fa-edit me-2"></i>Edit Kategori Skill</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="category_name" id="edit_cat_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="display_order" id="edit_cat_order" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info fw-semibold"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Skill Item -->
<div class="modal fade" id="editSkillModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border border-info text-light">
            <form action="" method="POST">
                <input type="hidden" name="update_skill" value="1">
                <input type="hidden" name="skill_id" id="edit_skill_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info"><i class="fas fa-edit me-2"></i>Edit Item Skill</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Kategori</label>
                            <select name="category_id" id="edit_skill_cat_id" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Skill / Item Kemampuan</label>
                            <input type="text" name="name" id="edit_skill_name" class="form-control" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Pilih Ikon Visual</label>
                            <input type="hidden" name="icon_class" id="edit_icon_class_input" value="fas fa-check-circle">
                            <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#iconPickerModal" id="btnOpenEditIconPicker">
                                <span class="d-flex align-items-center">
                                    <i id="edit_selected_icon_preview" class="fas fa-check-circle text-info me-2 fs-5"></i>
                                    <span id="edit_selected_icon_name" class="fw-semibold">Centang / Standar</span>
                                </span>
                                <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                            </button>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="display_order" id="edit_skill_order" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info fw-semibold"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visual Icon Picker -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border border-info text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-info"><i class="fas fa-icons me-2"></i>Pilih Ikon Skill</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="iconSearchInput" class="form-control" placeholder="Cari ikon... (contoh: network, code, wifi, check, server)">
                </div>
                <div class="row g-2 overflow-auto" style="max-height: 380px;" id="iconGridContainer">
                    <!-- Icon cards will be dynamically loaded by JS -->
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ICON_LIST = [
        // Umum / Simbol
        { class: 'fas fa-check-circle', name: 'Centang Lingkaran', category: 'Umum' },
        { class: 'fas fa-check', name: 'Centang Standar', category: 'Umum' },
        { class: 'fas fa-star', name: 'Bintang', category: 'Umum' },
        { class: 'fas fa-bolt', name: 'Petir / Fast', category: 'Umum' },
        { class: 'fas fa-shield-alt', name: 'Keamanan / Shield', category: 'Umum' },
        { class: 'fas fa-award', name: 'Penghargaan', category: 'Umum' },
        { class: 'fas fa-sparkles', name: 'Sparkles / Feature', category: 'Umum' },

        // IT & Jaringan / Support
        { class: 'fas fa-network-wired', name: 'Jaringan / LAN', category: 'IT Support' },
        { class: 'fas fa-sitemap', name: 'Sitemap / LAN', category: 'IT Support' },
        { class: 'fas fa-wifi', name: 'Wi-Fi / Wireless', category: 'IT Support' },
        { class: 'fas fa-server', name: 'Server / Infrastructure', category: 'IT Support' },
        { class: 'fas fa-desktop', name: 'Komputer Desktop', category: 'IT Support' },
        { class: 'fas fa-laptop-code', name: 'Laptop / Koding', category: 'IT Support' },
        { class: 'fas fa-tools', name: 'Perbaikan / Maintenance', category: 'IT Support' },
        { class: 'fas fa-wrench', name: 'Kunci Pas / Troubleshooting', category: 'IT Support' },
        { class: 'fas fa-video', name: 'CCTV / Video Recording', category: 'IT Support' },
        { class: 'fas fa-headset', name: 'Helpdesk / Support', category: 'IT Support' },
        { class: 'fas fa-cogs', name: 'Sistem / Maintenance', category: 'IT Support' },
        { class: 'fas fa-hdd', name: 'Storage / Harddrive', category: 'IT Support' },
        { class: 'fas fa-microchip', name: 'Hardware / Chip', category: 'IT Support' },

        // Web & Programming
        { class: 'fas fa-code', name: 'Kode / Programming', category: 'Software' },
        { class: 'fas fa-terminal', name: 'Terminal / Command Line', category: 'Software' },
        { class: 'fas fa-database', name: 'Database / SQL', category: 'Software' },
        { class: 'fas fa-globe', name: 'Website / Internet', category: 'Software' },
        { class: 'fas fa-chart-bar', name: 'Reporting / Laporan', category: 'Software' },
        { class: 'fas fa-robot', name: 'Otomatisasi / Automation', category: 'Software' },
        { class: 'fab fa-html5', name: 'HTML5', category: 'Software' },
        { class: 'fab fa-css3-alt', name: 'CSS3', category: 'Software' },
        { class: 'fab fa-js', name: 'JavaScript', category: 'Software' },
        { class: 'fab fa-php', name: 'PHP', category: 'Software' },
        { class: 'fab fa-python', name: 'Python', category: 'Software' },
        { class: 'fab fa-react', name: 'React JS', category: 'Software' },
        { class: 'fab fa-bootstrap', name: 'Bootstrap', category: 'Software' },
        { class: 'fab fa-node-js', name: 'Node JS', category: 'Software' },
        { class: 'fab fa-github', name: 'Git / GitHub', category: 'Software' },
        { class: 'fab fa-wordpress', name: 'WordPress', category: 'Software' },

        // Desain & Media
        { class: 'fas fa-palette', name: 'Palet Warna / Canva', category: 'Desain' },
        { class: 'fas fa-hashtag', name: 'Social Media', category: 'Desain' },
        { class: 'fas fa-film', name: 'Video Editing / CapCut', category: 'Desain' },
        { class: 'fas fa-file-alt', name: 'Dokumentasi / Konten', category: 'Desain' },
        { class: 'fas fa-photo-video', name: 'Visual Content', category: 'Desain' },
        { class: 'fas fa-paint-brush', name: 'Kuas Desain', category: 'Desain' },
        { class: 'fas fa-layer-group', name: 'Layer / Antarmuka', category: 'Desain' },
        { class: 'fas fa-camera', name: 'Fotografi / Media', category: 'Desain' },

        // Bisnis & Manajemen
        { class: 'fas fa-chart-line', name: 'Analisis & Data', category: 'Bisnis' },
        { class: 'fas fa-briefcase', name: 'Pengalaman / Kerja', category: 'Bisnis' },
        { class: 'fas fa-lightbulb', name: 'Inovasi / Ide', category: 'Bisnis' },
        { class: 'fas fa-cog', name: 'Pengaturan / Sistem', category: 'Bisnis' },
        { class: 'fas fa-users', name: 'Kolaborasi Tim', category: 'Bisnis' }
    ];

    let activeTargetMode = 'add'; // 'add' or 'edit'

    const iconGrid = document.getElementById('iconGridContainer');
    const searchInput = document.getElementById('iconSearchInput');
    const modalEl = document.getElementById('iconPickerModal');

    // Add Form Elements
    const addInputHidden = document.getElementById('icon_class_input');
    const addPreviewIcon = document.getElementById('selected_icon_preview');
    const addPreviewName = document.getElementById('selected_icon_name');

    // Edit Form Elements
    const editInputHidden = document.getElementById('edit_icon_class_input');
    const editPreviewIcon = document.getElementById('edit_selected_icon_preview');
    const editPreviewName = document.getElementById('edit_selected_icon_name');

    // Track active target mode when opening icon picker modal
    document.querySelectorAll('[data-bs-target="#iconPickerModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.id === 'btnOpenEditIconPicker') {
                activeTargetMode = 'edit';
            } else {
                activeTargetMode = 'add';
            }
            renderIcons();
        });
    });

    function renderIcons(filterText = '') {
        iconGrid.innerHTML = '';
        const search = filterText.toLowerCase();
        const currentIconValue = (activeTargetMode === 'edit') ? editInputHidden.value : addInputHidden.value;

        const filtered = ICON_LIST.filter(item => 
            item.name.toLowerCase().includes(search) || 
            item.class.toLowerCase().includes(search) ||
            item.category.toLowerCase().includes(search)
        );

        if (filtered.length === 0) {
            iconGrid.innerHTML = '<div class="col-12 text-center text-muted py-4">Tidak ada ikon yang sesuai pencarian.</div>';
            return;
        }

        filtered.forEach(item => {
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4 col-md-3';
            
            const isSelected = (currentIconValue === item.class);

            col.innerHTML = `
                <div class="p-3 border rounded text-center icon-card cursor-pointer ${isSelected ? 'border-info bg-info bg-opacity-10' : 'border-secondary border-opacity-25'}" 
                     style="cursor: pointer; transition: all 0.2s ease;">
                    <i class="${item.class} fs-3 text-info mb-2 d-block"></i>
                    <div class="small fw-semibold text-truncate text-light">${item.name}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">${item.category}</small>
                </div>
            `;

            col.addEventListener('click', function() {
                if (activeTargetMode === 'edit') {
                    editInputHidden.value = item.class;
                    editPreviewIcon.className = item.class + ' text-info me-2 fs-5';
                    editPreviewName.textContent = item.name;
                } else {
                    addInputHidden.value = item.class;
                    addPreviewIcon.className = item.class + ' text-info me-2 fs-5';
                    addPreviewName.textContent = item.name;
                }
                
                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });

            iconGrid.appendChild(col);
        });
    }

    renderIcons();

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            renderIcons(e.target.value);
        });
    }

    // Handle Edit Category Button Clicks
    document.querySelectorAll('.edit-cat-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_cat_id').value = this.dataset.id;
            document.getElementById('edit_cat_name').value = this.dataset.name;
            document.getElementById('edit_cat_order').value = this.dataset.order;
            
            const editCatModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editCatModal.show();
        });
    });

    // Handle Edit Skill Button Clicks
    document.querySelectorAll('.edit-skill-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_skill_id').value = this.dataset.id;
            document.getElementById('edit_skill_cat_id').value = this.dataset.catid;
            document.getElementById('edit_skill_name').value = this.dataset.name;
            document.getElementById('edit_skill_order').value = this.dataset.order;

            const iconClass = this.dataset.icon || 'fas fa-check-circle';
            editInputHidden.value = iconClass;
            editPreviewIcon.className = iconClass + ' text-info me-2 fs-5';
            
            // Find icon name
            const found = ICON_LIST.find(i => i.class === iconClass);
            editPreviewName.textContent = found ? found.name : 'Ikon Terpilih';

            const editSkillModal = new bootstrap.Modal(document.getElementById('editSkillModal'));
            editSkillModal.show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
