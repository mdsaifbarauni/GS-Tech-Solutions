<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - Inquiries Management
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

$page_title = "Inquiries Management | GS Tech Solutions";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            project_type VARCHAR(150) NOT NULL,
            budget VARCHAR(100) NULL,
            timeline VARCHAR(100) NULL,
            message TEXT NOT NULL,
            source VARCHAR(100) NULL DEFAULT 'website',
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            status ENUM('new', 'contacted', 'in_progress', 'completed', 'closed') NOT NULL DEFAULT 'new',
            assigned_to INT UNSIGNED NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Unable to initialize inquiries table.";
}

// ==========================================================
// Delete Inquiry
// ==========================================================

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['success_message'] = "Inquiry deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to delete inquiry.";
        }
    }

    header("Location: inquiries.php");
    exit;
}

// ==========================================================
// Update Status
// ==========================================================

if (isset($_GET['status'])) {
    $update_id = (int) ($_GET['id'] ?? 0);
    $status = trim($_GET['status'] ?? '');
    $allowed = ['new', 'contacted', 'in_progress', 'completed', 'closed'];

    if ($update_id > 0 && in_array($status, $allowed, true)) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ? WHERE id = ?");
            $stmt->execute([$status, $update_id]);
            $_SESSION['success_message'] = "Inquiry status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to update inquiry status.";
        }
    }

    header("Location: inquiries.php");
    exit;
}

// ==========================================================
// Search / Filters
// ==========================================================

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(
        name LIKE ?
        OR email LIKE ?
        OR project_type LIKE ?
        OR message LIKE ?
    )";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($status_filter !== '') {
    $where[] = "status = ?";
    $params[] = $status_filter;
}

$where_sql = '';
if (!empty($where)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare("
    SELECT *
    FROM contact_inquiries
    {$where_sql}
    ORDER BY id DESC
");
$stmt->execute($params);
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// Statistics
// ==========================================================

$total_inquiries = (int) $pdo->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();
$new_inquiries = (int) $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'")->fetchColumn();
$contacted_inquiries = (int) $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'contacted'")->fetchColumn();
$in_progress_inquiries = (int) $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'in_progress'")->fetchColumn();
$completed_inquiries = (int) $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status IN ('completed', 'closed')")->fetchColumn();

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
                <h1>Inquiries</h1>
                <p>Manage customer contact requests and follow-ups</p>
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
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>
                    <div>
                        <span>Total Inquiries</span>
                        <strong><?= number_format($total_inquiries) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <span>New</span>
                        <strong><?= number_format($new_inquiries) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon info">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <span>Contacted</span>
                        <strong><?= number_format($contacted_inquiries) ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <span>Completed</span>
                        <strong><?= number_format($completed_inquiries) ?></strong>
                    </div>
                </div>
            </div>

        </div>

        <div class="admin-panel">

            <div class="panel-header">
                <div>
                    <h2>Contact Inbox</h2>
                    <p>Review and update incoming customer inquiries.</p>
                </div>

                <form method="GET" action="inquiries.php" class="admin-filter-form">
                    <div class="input-group">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="<?= e($search) ?>"
                            placeholder="Search inquiries"
                        >
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="new" <?= ($status_filter === 'new') ? 'selected' : '' ?>>New</option>
                            <option value="contacted" <?= ($status_filter === 'contacted') ? 'selected' : '' ?>>Contacted</option>
                            <option value="in_progress" <?= ($status_filter === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="completed" <?= ($status_filter === 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="closed" <?= ($status_filter === 'closed') ? 'selected' : '' ?>>Closed</option>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inquiries)): ?>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?= (int) $inquiry['id'] ?></td>
                                    <td>
                                        <strong><?= e($inquiry['name']) ?></strong>
                                        <?php if (!empty($inquiry['phone'])): ?>
                                            <small class="d-block text-muted"><?= e($inquiry['phone']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($inquiry['email']) ?></td>
                                    <td>
                                        <div class="subject-col">
                                            <strong><?= e($inquiry['project_type'] ?? 'General Inquiry') ?></strong>
                                            <small><?= e(substr($inquiry['message'], 0, 120)) ?><?= strlen((string) $inquiry['message']) > 120 ? '...' : '' ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $status = $inquiry['status'] ?? 'new';
                                            $badge = [
                                                'new' => 'bg-warning text-dark',
                                                'contacted' => 'bg-primary',
                                                'in_progress' => 'bg-info text-dark',
                                                'completed' => 'bg-success',
                                                'closed' => 'bg-secondary'
                                            ][$status] ?? 'bg-light text-dark';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= e(str_replace('_', ' ', ucfirst($status))) ?></span>
                                    </td>
                                    <td><?= e(date('d M Y', strtotime($inquiry['created_at']))) ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="inquiries.php?id=<?= (int) $inquiry['id'] ?>&status=new">New</a></li>
                                                    <li><a class="dropdown-item" href="inquiries.php?id=<?= (int) $inquiry['id'] ?>&status=contacted">Contacted</a></li>
                                                    <li><a class="dropdown-item" href="inquiries.php?id=<?= (int) $inquiry['id'] ?>&status=in_progress">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="inquiries.php?id=<?= (int) $inquiry['id'] ?>&status=completed">Completed</a></li>
                                                    <li><a class="dropdown-item" href="inquiries.php?id=<?= (int) $inquiry['id'] ?>&status=closed">Closed</a></li>
                                                </ul>
                                            </div>
                                            <a href="inquiries.php?delete=<?= (int) $inquiry['id'] ?>" class="btn btn-sm btn-outline-danger delete-btn" title="Delete inquiry" data-name="<?= e($inquiry['project_type'] ?? 'General Inquiry') ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No inquiries found.
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
                const name = button.getAttribute('data-name') || 'this inquiry';
                if (!confirm('Delete "' + name + '"?')) {
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
    .admin-stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .admin-stat-icon.info { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
    .admin-stat-icon.success { background: linear-gradient(135deg, #16a34a, #4ade80); }

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

    .admin-filter-form {
        width: min(100%, 560px);
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
        vertical-align: top;
        color: var(--gs-text, #243447);
        border-color: var(--gs-border, #e8edf3);
        padding: 16px 12px;
    }

    .subject-col {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 220px;
    }

    .subject-col small {
        color: var(--gs-muted, #7b8794);
        line-height: 1.5;
    }

    .table-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
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
