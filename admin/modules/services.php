<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helper.php';

$page_title = 'Kelola Services / Layanan';
$active_menu = 'services';

$action = $_GET['action'] ?? 'list';

// Handle Delete Service
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    db()->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
    set_flash('success', 'Layanan berhasil dihapus!');
    header("Location: " . ADMIN_URL . "modules/services.php");
    exit;
}

// Handle Add Service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    $title = $_POST['title'] ?? '';
    $icon_class = $_POST['icon_class'] ?? 'fas fa-laptop-code';
    $short_description = $_POST['short_description'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    if (!empty($title)) {
        $stmt = db()->prepare("INSERT INTO services (title, icon_class, short_description, display_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $icon_class, $short_description, $display_order]);
        set_flash('success', 'Layanan baru berhasil ditambahkan!');
    }
    header("Location: " . ADMIN_URL . "modules/services.php");
    exit;
}

// Handle Update Service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $service_id = $_POST['service_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $icon_class = $_POST['icon_class'] ?? 'fas fa-laptop-code';
    $short_description = $_POST['short_description'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    if ($service_id > 0 && !empty($title)) {
        $stmt = db()->prepare("UPDATE services SET title = ?, icon_class = ?, short_description = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$title, $icon_class, $short_description, $display_order, $service_id]);
        set_flash('success', 'Layanan berhasil diperbarui!');
    }
    header("Location: " . ADMIN_URL . "modules/services.php");
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$services = db()->query("SELECT * FROM services ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="row g-4 mb-4">
    <!-- Add Service Form -->
    <div class="col-md-5">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-plus-circle me-2"></i>Tambah Layanan Baru</h5>
            <form action="" method="POST">
                <input type="hidden" name="save_service" value="1">
                <div class="mb-3">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Website Development" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Ikon Visual</label>
                    <input type="hidden" name="icon_class" id="icon_class_input" value="fas fa-laptop-code">
                    <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#serviceIconPickerModal" id="btnOpenAddIconPicker">
                        <span class="d-flex align-items-center">
                            <i id="selected_icon_preview" class="fas fa-laptop-code text-info me-2 fs-5"></i>
                            <span id="selected_icon_name" class="fw-semibold">Laptop / Coding</span>
                        </span>
                        <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Layanan</label>
                    <textarea name="short_description" class="form-control" rows="3" placeholder="Tuliskan deskripsi singkat layanan..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="display_order" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-info w-100 fw-semibold py-2"><i class="fas fa-save me-1"></i> Simpan Layanan</button>
            </form>
        </div>
    </div>

    <!-- Services List Table -->
    <div class="col-md-7">
        <div class="glass-card p-4">
            <h5 class="text-info mb-3"><i class="fas fa-cogs me-2"></i>Daftar Layanan</h5>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Ikon</th>
                            <th>Layanan</th>
                            <th>Deskripsi</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Belum ada layanan ditambahkan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($services as $srv): ?>
                                <tr>
                                    <td>
                                        <div class="bg-dark p-2 rounded text-center border border-secondary border-opacity-25">
                                            <i class="<?= sanitize(!empty($srv['icon_class']) ? $srv['icon_class'] : 'fas fa-cog') ?> text-info fs-4"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-white fs-6"><?= sanitize($srv['title']) ?></strong>
                                        <div class="small text-muted">Urutan: <?= $srv['display_order'] ?></div>
                                    </td>
                                    <td><small class="text-muted"><?= sanitize($srv['short_description']) ?></small></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="btn btn-sm btn-outline-info edit-srv-btn me-1"
                                                data-id="<?= $srv['id'] ?>"
                                                data-title="<?= sanitize($srv['title']) ?>"
                                                data-icon="<?= sanitize($srv['icon_class'] ?? 'fas fa-laptop-code') ?>"
                                                data-desc="<?= sanitize($srv['short_description']) ?>"
                                                data-order="<?= $srv['display_order'] ?>">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <a href="<?= ADMIN_URL ?>modules/services.php?action=delete&id=<?= $srv['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus layanan ini?')">
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

<!-- Modal Edit Service -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border border-info text-light">
            <form action="" method="POST">
                <input type="hidden" name="update_service" value="1">
                <input type="hidden" name="service_id" id="edit_service_id">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info"><i class="fas fa-edit me-2"></i>Edit Layanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Nama Layanan</label>
                            <input type="text" name="title" id="edit_service_title" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="display_order" id="edit_service_order" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pilih Ikon Visual</label>
                            <input type="hidden" name="icon_class" id="edit_service_icon_class" value="fas fa-laptop-code">
                            <button type="button" class="btn btn-outline-info w-100 text-start d-flex align-items-center justify-content-between py-2" data-bs-toggle="modal" data-bs-target="#serviceIconPickerModal" id="btnOpenEditServiceIconPicker">
                                <span class="d-flex align-items-center">
                                    <i id="edit_selected_icon_preview" class="fas fa-laptop-code text-info me-2 fs-5"></i>
                                    <span id="edit_selected_icon_name" class="fw-semibold">Laptop / Coding</span>
                                </span>
                                <span class="badge bg-info text-dark">Ganti Ikon <i class="fas fa-mouse-pointer ms-1"></i></span>
                            </button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi Layanan</label>
                            <textarea name="short_description" id="edit_service_desc" class="form-control" rows="3" required></textarea>
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
<div class="modal fade" id="serviceIconPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border border-info text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-info"><i class="fas fa-icons me-2"></i>Pilih Ikon Layanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="iconSearchInput" class="form-control" placeholder="Cari ikon... (contoh: laptop, network, video, palette, film, globe, server)">
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
    const SERVICE_ICONS = [
        { class: 'fas fa-laptop-code', name: 'Laptop / Coding', category: 'Web & Digital' },
        { class: 'fas fa-globe', name: 'Website / Global', category: 'Web & Digital' },
        { class: 'fas fa-network-wired', name: 'Jaringan / LAN', category: 'IT Support' },
        { class: 'fas fa-video', name: 'CCTV / Video', category: 'IT & CCTV' },
        { class: 'fas fa-palette', name: 'Desain / Canva', category: 'Desain & Media' },
        { class: 'fas fa-film', name: 'Video Editing / CapCut', category: 'Desain & Media' },
        { class: 'fas fa-code', name: 'Koding / Web', category: 'Web & Digital' },
        { class: 'fas fa-desktop', name: 'Komputer Desktop', category: 'IT Support' },
        { class: 'fas fa-server', name: 'Server / Hosting', category: 'IT Support' },
        { class: 'fas fa-tools', name: 'Perbaikan / Maintenance', category: 'IT Support' },
        { class: 'fas fa-wrench', name: 'Troubleshooting / Kunci Pas', category: 'IT Support' },
        { class: 'fas fa-chart-bar', name: 'Reporting / Diagram', category: 'Web & Digital' },
        { class: 'fas fa-robot', name: 'Automasi / AI', category: 'Web & Digital' },
        { class: 'fas fa-database', name: 'Database / Data', category: 'Web & Digital' },
        { class: 'fas fa-photo-video', name: 'Multimedia', category: 'Desain & Media' },
        { class: 'fas fa-hashtag', name: 'Social Media', category: 'Desain & Media' },
        { class: 'fas fa-file-alt', name: 'Dokumentasi / Konten', category: 'Desain & Media' }
    ];

    let activeTargetMode = 'add'; // 'add' or 'edit'

    const iconGrid = document.getElementById('iconGridContainer');
    const searchInput = document.getElementById('iconSearchInput');
    const modalEl = document.getElementById('serviceIconPickerModal');

    // Add Form Elements
    const addInputHidden = document.getElementById('icon_class_input');
    const addPreviewIcon = document.getElementById('selected_icon_preview');
    const addPreviewName = document.getElementById('selected_icon_name');

    // Edit Form Elements
    const editInputHidden = document.getElementById('edit_service_icon_class');
    const editPreviewIcon = document.getElementById('edit_selected_icon_preview');
    const editPreviewName = document.getElementById('edit_selected_icon_name');

    // Track active target mode when opening icon picker modal
    document.querySelectorAll('[data-bs-target="#serviceIconPickerModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.id === 'btnOpenEditServiceIconPicker') {
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

        const filtered = SERVICE_ICONS.filter(item => 
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

    // Handle Edit Service Button Clicks
    document.querySelectorAll('.edit-srv-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_service_id').value = this.dataset.id;
            document.getElementById('edit_service_title').value = this.dataset.title;
            document.getElementById('edit_service_desc').value = this.dataset.desc;
            document.getElementById('edit_service_order').value = this.dataset.order;

            const iconClass = this.dataset.icon || 'fas fa-laptop-code';
            editInputHidden.value = iconClass;
            editPreviewIcon.className = iconClass + ' text-info me-2 fs-5';

            const found = SERVICE_ICONS.find(i => i.class === iconClass);
            editPreviewName.textContent = found ? found.name : 'Ikon Terpilih';

            const editModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
            editModal.show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
