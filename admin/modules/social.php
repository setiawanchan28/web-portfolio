<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';

$page_title = 'Kelola Social Media Links';
$active_menu = 'social';

$action = $_GET['action'] ?? 'list';

// Handle Delete Social Link
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    db()->prepare("DELETE FROM social_links WHERE id = ?")->execute([$id]);
    set_flash('success', 'Social Link berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/social.php");
    exit;
}

// Handle Save Social Link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_social'])) {
    $platform = $_POST['platform'] ?? '';
    $url = $_POST['url'] ?? '';
    $icon_class = $_POST['icon_class'] ?? 'fab fa-instagram';
    $display_order = $_POST['display_order'] ?? 0;

    if (!empty($platform) && !empty($url)) {
        $stmt = db()->prepare("INSERT INTO social_links (platform, url, icon_class, display_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$platform, $url, $icon_class, $display_order]);
        set_flash('success', 'Social link baru berhasil ditambahkan!');
    }
    header("Location: " . ADMIN_URL . "modules/social.php");
    exit;
}

// Handle Update Social Link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
    $social_id = $_POST['social_id'] ?? 0;
    $platform = $_POST['platform'] ?? '';
    $url = $_POST['url'] ?? '';
    $icon_class = $_POST['icon_class'] ?? 'fab fa-instagram';
    $display_order = $_POST['display_order'] ?? 0;

    if ($social_id > 0 && !empty($platform) && !empty($url)) {
        $stmt = db()->prepare("UPDATE social_links SET platform = ?, url = ?, icon_class = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$platform, $url, $icon_class, $display_order, $social_id]);
        set_flash('success', 'Social link berhasil diperbarui!');
    }
    header("Location: " . ADMIN_URL . "modules/social.php");
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$socials = db()->query("SELECT * FROM social_links ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="row g-4 mb-4">
    <!-- Add Social Form -->
    <div class="col-md-5">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-plus-circle me-2"></i>Tambah Social Link</h5>
            <form action="" method="POST">
                <input type="hidden" name="save_social" value="1">
                <div class="mb-3">
                    <label class="form-label">Nama Platform</label>
                    <input type="text" name="platform" class="form-control" placeholder="Contoh: Instagram, GitHub, WhatsApp, LinkedIn" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL Profil / Link</label>
                    <input type="url" name="url" class="form-control" placeholder="https://instagram.com/setiawanchan_" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Ikon Visual</label>
                    <input type="hidden" name="icon_class" id="icon_class_input" value="fab fa-instagram">
                    <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#socialIconPickerModal" id="btnOpenAddIconPicker">
                        <span class="d-flex align-items-center">
                            <i id="selected_icon_preview" class="fab fa-instagram text-info me-2 fs-5"></i>
                            <span id="selected_icon_name" class="fw-semibold">Instagram</span>
                        </span>
                        <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="display_order" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-info w-100 fw-semibold py-2"><i class="fas fa-save me-1"></i> Simpan Social Link</button>
            </form>
        </div>
    </div>

    <!-- Social Links Table -->
    <div class="col-md-7">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-share-alt me-2"></i>Daftar Social Links</h5>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Ikon</th>
                            <th>Platform</th>
                            <th>URL Profil</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($socials)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Belum ada link social media.</td></tr>
                        <?php else: ?>
                            <?php foreach ($socials as $soc): ?>
                                <tr>
                                    <td>
                                        <div class="bg-dark p-2 rounded text-center border border-secondary border-opacity-25">
                                            <i class="<?= sanitize(!empty($soc['icon_class']) ? $soc['icon_class'] : 'fas fa-globe') ?> text-info fs-4"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-white fs-6"><?= sanitize($soc['platform']) ?></strong>
                                        <div class="small text-muted">Urutan: <?= $soc['display_order'] ?></div>
                                    </td>
                                    <td><small class="text-info text-break"><?= sanitize($soc['url']) ?></small></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="btn btn-sm btn-outline-info edit-soc-btn me-1"
                                                data-id="<?= $soc['id'] ?>"
                                                data-platform="<?= sanitize($soc['platform']) ?>"
                                                data-url="<?= sanitize($soc['url']) ?>"
                                                data-icon="<?= sanitize($soc['icon_class'] ?? 'fab fa-instagram') ?>"
                                                data-order="<?= $soc['display_order'] ?>">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <a href="<?= ADMIN_URL ?>modules/social.php?action=delete&id=<?= $soc['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus link sosial media ini?')">
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
    </div>
</div>

<!-- Modal Edit Social Link -->
<div class="modal fade" id="editSocialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-info text-light">
            <form action="" method="POST">
                <input type="hidden" name="update_social" value="1">
                <input type="hidden" name="social_id" id="edit_social_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info"><i class="fas fa-edit me-2"></i>Edit Social Link</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Nama Platform</label>
                            <input type="text" name="platform" id="edit_social_platform" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="display_order" id="edit_social_order" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">URL Profil / Link</label>
                            <input type="url" name="url" id="edit_social_url" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pilih Ikon Visual</label>
                            <input type="hidden" name="icon_class" id="edit_social_icon_class" value="fab fa-instagram">
                            <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#socialIconPickerModal" id="btnOpenEditSocialIconPicker">
                                <span class="d-flex align-items-center">
                                    <i id="edit_selected_icon_preview" class="fab fa-instagram text-info me-2 fs-5"></i>
                                    <span id="edit_selected_icon_name" class="fw-semibold">Instagram</span>
                                </span>
                                <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                            </button>
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
<div class="modal fade" id="socialIconPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border border-info text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-info"><i class="fas fa-icons me-2"></i>Pilih Ikon Social Media</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="iconSearchInput" class="form-control" placeholder="Cari platform... (contoh: instagram, whatsapp, github, linkedin, youtube, facebook)">
                </div>
                <div class="row g-2 overflow-auto" style="max-height: 380px;" id="iconGridContainer">
                    <!-- Dynamic icons via JS -->
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
    const SOCIAL_ICONS = [
        { class: 'fab fa-instagram', name: 'Instagram', category: 'Media Sosial' },
        { class: 'fab fa-whatsapp', name: 'WhatsApp', category: 'Chat & Kontak' },
        { class: 'fab fa-github', name: 'GitHub', category: 'Developer' },
        { class: 'fab fa-linkedin', name: 'LinkedIn', category: 'Profesional' },
        { class: 'fab fa-youtube', name: 'YouTube', category: 'Video' },
        { class: 'fab fa-tiktok', name: 'TikTok', category: 'Video' },
        { class: 'fab fa-facebook', name: 'Facebook', category: 'Media Sosial' },
        { class: 'fab fa-x-twitter', name: 'X / Twitter', category: 'Media Sosial' },
        { class: 'fab fa-telegram', name: 'Telegram', category: 'Chat & Kontak' },
        { class: 'fab fa-discord', name: 'Discord', category: 'Chat & Komunitas' },
        { class: 'fab fa-spotify', name: 'Spotify', category: 'Audio' },
        { class: 'fas fa-envelope', name: 'Email / Direct', category: 'Kontak' },
        { class: 'fas fa-globe', name: 'Website Portofolio / Blog', category: 'Website' }
    ];

    let activeTargetMode = 'add'; // 'add' or 'edit'

    const iconGrid = document.getElementById('iconGridContainer');
    const searchInput = document.getElementById('iconSearchInput');
    const modalEl = document.getElementById('socialIconPickerModal');

    // Add Form Elements
    const addInputHidden = document.getElementById('icon_class_input');
    const addPreviewIcon = document.getElementById('selected_icon_preview');
    const addPreviewName = document.getElementById('selected_icon_name');

    // Edit Form Elements
    const editInputHidden = document.getElementById('edit_social_icon_class');
    const editPreviewIcon = document.getElementById('edit_selected_icon_preview');
    const editPreviewName = document.getElementById('edit_selected_icon_name');

    document.querySelectorAll('[data-bs-target="#socialIconPickerModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.id === 'btnOpenEditSocialIconPicker') {
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

        const filtered = SOCIAL_ICONS.filter(item => 
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
                <div class="p-3 border rounded text-center icon-card ${isSelected ? 'border-info bg-info bg-opacity-10' : 'border-secondary border-opacity-25'}" 
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

    // Handle Edit Social Link Button Clicks
    document.querySelectorAll('.edit-soc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_social_id').value = this.dataset.id;
            document.getElementById('edit_social_platform').value = this.dataset.platform;
            document.getElementById('edit_social_url').value = this.dataset.url;
            document.getElementById('edit_social_order').value = this.dataset.order;

            const iconClass = this.dataset.icon || 'fab fa-instagram';
            editInputHidden.value = iconClass;
            editPreviewIcon.className = iconClass + ' text-info me-2 fs-5';

            const found = SOCIAL_ICONS.find(i => i.class === iconClass);
            editPreviewName.textContent = found ? found.name : 'Ikon Terpilih';

            const editModal = new bootstrap.Modal(document.getElementById('editSocialModal'));
            editModal.show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
