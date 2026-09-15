<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - Services Management
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['admin_id']) ||
    (int) $_SESSION['admin_id'] <= 0 ||
    ($_SESSION['admin_role'] ?? '') !== 'super_admin'
) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$page_title = "Services Management | GS Tech Solutions";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            service_name VARCHAR(150) NOT NULL,
            slug VARCHAR(180) NOT NULL,
            short_description VARCHAR(500) NULL,
            description TEXT NULL,
            icon VARCHAR(100) NULL,
            image VARCHAR(255) NULL,
            service_number VARCHAR(10) NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_service_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Unable to initialize services table.";
}

// ==========================================================
// Delete Service
// ==========================================================

if (isset($_GET['delete'])) {

    $delete_id = (int) $_GET['delete'];

    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['success_message'] = "Service deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to delete service.";
        }
    }

    header("Location: services.php");
    exit;
}

// ==========================================================
// Toggle Service Status
// ==========================================================

if (isset($_GET['toggle'])) {

    $toggle_id = (int) $_GET['toggle'];

    if ($toggle_id > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE services
                SET status = IF(status = 1, 0, 1)
                WHERE id = ?
            ");
            $stmt->execute([$toggle_id]);
            $_SESSION['success_message'] = "Service status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to update service status.";
        }
    }

    header("Location: services.php");
    exit;
}

// ==========================================================
// Get Editable Service
// ==========================================================

$edit_service = null;

if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];

    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_id]);
            $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $edit_service = null;
        }
    }
}

// ==========================================================
// Add / Edit Service
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $service_id = (int) ($_POST['service_id'] ?? 0);

    $service_name = trim($_POST['service_name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'bi bi-grid');
    $image = trim($_POST['image'] ?? '');
    $service_number = trim($_POST['service_number'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $sort_order = (int) ($_POST['sort_order'] ?? 0);

    if ($service_name === '') {
        $_SESSION['error_message'] = "Service name is required.";
        header("Location: services.php");
        exit;
    }

    if ($short_description === '') {
        $_SESSION['error_message'] = "Short description is required.";
        header("Location: services.php");
        exit;
    }

    if ($slug === '') {
        $slug = strtolower($service_name);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
    }

    if ($service_number === '') {
        $service_number = str_pad((string) ($sort_order > 0 ? $sort_order : ($service_id > 0 ? $service_id : count($services) + 1)), 2, '0', STR_PAD_LEFT);
    }

    if ($slug === '') {
        $_SESSION['error_message'] = "Service slug is required.";
        header("Location: services.php");
        exit;
    }

    if ($icon === '') {
        $icon = 'bi bi-grid';
    }

    try {

        if ($action === 'add') {

            $check = $pdo->prepare("SELECT id FROM services WHERE slug = ? LIMIT 1");
            $check->execute([$slug]);

            if ($check->fetch()) {
                $_SESSION['error_message'] = "A service with this slug already exists.";
                header("Location: services.php");
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO services (
                    service_name,
                    slug,
                    short_description,
                    description,
                    icon,
                    image,
                    service_number,
                    status,
                    sort_order,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->execute([
                $service_name,
                $slug,
                $short_description,
                $description,
                $icon,
                $image,
                $service_number,
                $status,
                $sort_order
            ]);

            $_SESSION['success_message'] = "Service added successfully.";
        }

        elseif ($action === 'edit' && $service_id > 0) {

            $check = $pdo->prepare("
                SELECT id
                FROM services
                WHERE slug = ? AND id != ?
                LIMIT 1
            ");
            $check->execute([$slug, $service_id]);

            if ($check->fetch()) {
                $_SESSION['error_message'] = "Another service already uses this slug.";
                header("Location: services.php");
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE services
                SET
                    service_name = ?,
                    slug = ?,
                    short_description = ?,
                    description = ?,
                    icon = ?,
                    image = ?,
                    service_number = ?,
                    status = ?,
                    sort_order = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $service_name,
                $slug,
                $short_description,
                $description,
                $icon,
                $image,
                $service_number,
                $status,
                $sort_order,
                $service_id
            ]);

            $_SESSION['success_message'] = "Service updated successfully.";
        }

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error. Please try again.";
    }

    header("Location: services.php");
    exit;
}

// ==========================================================
// Search / Filters
// ==========================================================

$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(
        service_name LIKE ?
        OR short_description LIKE ?
        OR description LIKE ?
    )";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($status_filter !== '') {
    $where[] = "status = ?";
    $params[] = (int) $status_filter;
}

$where_sql = '';
if (!empty($where)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare("
    SELECT *
    FROM services
    {$where_sql}
    ORDER BY sort_order ASC, id DESC
");
$stmt->execute($params);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// Statistics
// ==========================================================

$total_services = (int) $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$active_services = (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status = 1")->fetchColumn();
$inactive_services = (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status = 0")->fetchColumn();
$featured_services = (int) $pdo->query("SELECT COUNT(*) FROM services WHERE image IS NOT NULL AND TRIM(image) <> ''")->fetchColumn();

// ==========================================================
// Flash Messages
// ==========================================================

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="main">

    <header class="topbar">

        <div class="d-flex align-items-center gap-3">

            <button
                type="button"
                class="mobile-menu"
                id="mobileMenu"
                aria-label="Open Menu"
            >
                <i class="bi bi-list"></i>
            </button>

            <div class="page-heading">
                <h1>Services</h1>
                <p>Manage website services and offerings</p>
            </div>

        </div>

        <div class="topbar-right">
            <div class="top-admin">
                <div class="top-admin-avatar">
                    <?= e(getInitials($_SESSION['admin_name'] ?? 'Administrator')) ?>
                </div>
                <div class="top-admin-info">
                    <strong><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></strong>
                    <span>Super Admin</span>
                </div>
            </div>
        </div>

    </header>

    <div class="content-body">

        <?php if ($success_message !== ''): ?>
            <div class="alert alert-success alert-dismissible fade show gs-admin-alert" role="alert">
                <?= e($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message !== ''): ?>
            <div class="alert alert-danger alert-dismissible fade show gs-admin-alert" role="alert">
                <?= e($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon blue">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <span>Total Services</span>
                        <strong><?= number_format($total_services) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <span>Active</span>
                        <strong><?= number_format($active_services) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon warning">
                        <i class="bi bi-slash-circle-fill"></i>
                    </div>
                    <div>
                        <span>Inactive</span>
                        <strong><?= number_format($inactive_services) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon purple">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <span>With Image</span>
                        <strong><?= number_format($featured_services) ?></strong>
                    </div>
                </div>
            </div>

        </div>

        <div class="admin-panel">

            <div class="panel-header">
                <div>
                    <h2><?= $edit_service ? 'Edit Service' : 'Add New Service' ?></h2>
                    <p><?= $edit_service ? 'Update the selected service details.' : 'Create a new service listing for the website.' ?></p>
                </div>
            </div>

            <form method="POST" action="services.php" class="gs-service-form">
                <input type="hidden" name="action" value="<?= $edit_service ? 'edit' : 'add' ?>">
                <input type="hidden" name="service_id" value="<?= (int) ($edit_service['id'] ?? 0) ?>">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label">Service Title</label>
                        <input
                            type="text"
                            name="service_name"
                            class="form-control"
                            value="<?= e($edit_service['service_name'] ?? '') ?>"
                            placeholder="Website Design & Development"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL Slug</label>
                        <input
                            type="text"
                            name="slug"
                            class="form-control"
                            value="<?= e($edit_service['slug'] ?? '') ?>"
                            placeholder="website-design-development"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Icon Class</label>
                        <input
                            type="text"
                            name="icon"
                            class="form-control"
                            value="<?= e($edit_service['icon'] ?? 'bi bi-grid') ?>"
                            placeholder="bi bi-code-square"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Service Number</label>
                        <input
                            type="text"
                            name="service_number"
                            class="form-control"
                            value="<?= e($edit_service['service_number'] ?? '') ?>"
                            placeholder="01"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sort Order</label>
                        <input
                            type="number"
                            name="sort_order"
                            class="form-control"
                            value="<?= e((string) ($edit_service['sort_order'] ?? 0)) ?>"
                            min="0"
                        >
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch mt-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="serviceStatus"
                                name="status"
                                <?= (!empty($edit_service) ? ((int) ($edit_service['status'] ?? 0) === 1 ? 'checked' : '') : 'checked') ?>
                            >
                            <label class="form-check-label" for="serviceStatus">Active</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Image URL</label>
                        <input
                            type="text"
                            name="image"
                            class="form-control"
                            value="<?= e($edit_service['image'] ?? '') ?>"
                            placeholder="assets/img/services/website.jpg"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Icon Class</label>
                        <input
                            type="text"
                            name="icon"
                            class="form-control"
                            value="<?= e($edit_service['icon'] ?? 'bi bi-grid') ?>"
                            placeholder="bi bi-code-square"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <textarea
                            name="short_description"
                            class="form-control"
                            rows="3"
                            placeholder="Short summary of the service"
                            required
                        ><?= e($edit_service['short_description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Full Description</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="6"
                            placeholder="Detailed description of what this service includes"
                        ><?= e($edit_service['description'] ?? '') ?></textarea>
                    </div>

                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        <?= $edit_service ? 'Update Service' : 'Save Service' ?>
                    </button>

                    <?php if ($edit_service): ?>
                        <a href="services.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>

        </div>

        <div class="admin-panel mt-4">

            <div class="panel-header">
                <div>
                    <h2>Service List</h2>
                    <p>Review and manage all available services.</p>
                </div>

                <form method="GET" action="services.php" class="admin-filter-form">
                    <div class="input-group">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="<?= e($search) ?>"
                            placeholder="Search services"
                        >
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" <?= ($status_filter === '1') ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ($status_filter === '0') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover admin-data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th>Slug</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= (int) $service['id'] ?></td>
                                    <td>
                                        <div class="service-cell">
                                            <div class="service-icon-box">
                                                <i class="<?= e($service['icon'] ?? 'bi bi-grid') ?>"></i>
                                            </div>
                                            <div>
                                                <strong><?= e($service['service_name'] ?? $service['title'] ?? '') ?></strong>
                                                <small><?= e($service['short_description'] ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= e($service['slug']) ?></td>
                                    <td>
                                        <?php if (!empty($service['image'])): ?>
                                            <span class="badge bg-primary">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $service['status'] === 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int) $service['sort_order'] ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="services.php?edit=<?= (int) $service['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="services.php?toggle=<?= (int) $service['id'] ?>" class="btn btn-sm btn-outline-warning" title="Toggle status">
                                                <i class="bi bi-power"></i>
                                            </a>
                                            <a href="services.php?delete=<?= (int) $service['id'] ?>" class="btn btn-sm btn-outline-danger delete-btn" title="Delete service" data-name="<?= e($service['service_name'] ?? $service['title'] ?? '') ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No services found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(function (button) {
            button.addEventListener('click', function (event) {
                const serviceName = button.getAttribute('data-name') || 'this service';
                if (!confirm('Delete ' + serviceName + '?')) {
                    event.preventDefault();
                }
            });
        });
    });
</script>

<style>
    .content-body {
        padding: 30px 24px 40px;
    }

    .gs-admin-alert {
        margin-bottom: 20px;
    }

    .admin-stat-card {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--gs-card, #fff);
        border: 1px solid var(--gs-border, #e8edf3);
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
        padding: 20px 18px;
    }

    .admin-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
    }

    .admin-stat-icon.blue { background: linear-gradient(135deg, #0d6efd, #4ea8ff); }
    .admin-stat-icon.success { background: linear-gradient(135deg, #16a34a, #4ade80); }
    .admin-stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .admin-stat-icon.purple { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

    .admin-stat-card span {
        display: block;
        color: var(--gs-muted, #7b8794);
        font-size: 0.8rem;
        margin-bottom: 6px;
    }

    .admin-stat-card strong {
        font-size: 1.8rem;
        color: var(--gs-dark, #071a2b);
        line-height: 1.1;
    }

    .admin-panel {
        background: var(--gs-card, #fff);
        border: 1px solid var(--gs-border, #e8edf3);
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(8, 29, 48, 0.06);
        padding: 24px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .panel-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--gs-dark, #071a2b);
    }

    .panel-header p {
        margin: 6px 0 0;
        color: var(--gs-muted, #7b8794);
    }

    .gs-service-form .form-label {
        font-weight: 600;
        color: var(--gs-text, #243447);
        margin-bottom: 8px;
    }

    .gs-service-form .form-control,
    .gs-service-form .form-select,
    .admin-filter-form .form-control,
    .admin-filter-form .form-select {
        border-radius: 12px;
        border: 1px solid var(--gs-border, #e8edf3);
        padding: 0.7rem 0.9rem;
        box-shadow: none;
    }

    .gs-service-form .form-control:focus,
    .gs-service-form .form-select:focus,
    .admin-filter-form .form-control:focus,
    .admin-filter-form .form-select:focus {
        border-color: rgba(13, 110, 253, 0.4);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .admin-filter-form {
        width: min(100%, 520px);
    }

    .admin-data-table {
        margin-bottom: 0;
    }

    .admin-data-table th {
        color: var(--gs-muted, #7b8794);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid var(--gs-border, #e8edf3);
    }

    .admin-data-table td {
        vertical-align: middle;
        color: var(--gs-text, #243447);
        border-color: var(--gs-border, #e8edf3);
        padding: 16px 12px;
    }

    .service-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .service-cell strong {
        display: block;
        margin-bottom: 4px;
    }

    .service-cell small {
        display: block;
        color: var(--gs-muted, #7b8794);
        line-height: 1.4;
        max-width: 260px;
    }

    .service-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(13, 110, 253, 0.1);
        color: var(--gs-primary, #0d6efd);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .table-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    @media (max-width: 767px) {
        .content-body {
            padding: 20px 14px 30px;
        }

        .admin-panel {
            padding: 18px 14px;
        }

        .admin-stat-card {
            padding: 18px 14px;
        }
    }
</style>

<?php
// ==========================================================
// END OF PAGE
// ==========================================================
?>
