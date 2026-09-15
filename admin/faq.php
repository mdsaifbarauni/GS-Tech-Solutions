<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - FAQ Management
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

$page_title = "FAQ Management | GS Tech Solutions";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS faq_items (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Unable to initialize FAQ table.";
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM faq_items WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['success_message'] = "FAQ deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to delete FAQ.";
        }
    }

    header("Location: faq.php");
    exit;
}

if (isset($_GET['toggle'])) {
    $toggle_id = (int) $_GET['toggle'];

    if ($toggle_id > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE faq_items
                SET status = IF(status = 1, 0, 1)
                WHERE id = ?
            ");
            $stmt->execute([$toggle_id]);
            $_SESSION['success_message'] = "FAQ status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to update FAQ status.";
        }
    }

    header("Location: faq.php");
    exit;
}

$edit_faq = null;

if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];

    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM faq_items WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_id]);
            $edit_faq = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $edit_faq = null;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $faq_id = (int) ($_POST['faq_id'] ?? 0);

    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $sort_order = (int) ($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;

    if ($question === '' || $answer === '') {
        $_SESSION['error_message'] = "Question and answer are required.";
        header("Location: faq.php");
        exit;
    }

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("
                INSERT INTO faq_items (
                    question,
                    answer,
                    sort_order,
                    status
                ) VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $question,
                $answer,
                $sort_order,
                $status
            ]);

            $_SESSION['success_message'] = "FAQ added successfully.";
        } elseif ($action === 'edit' && $faq_id > 0) {
            $stmt = $pdo->prepare("
                UPDATE faq_items
                SET
                    question = ?,
                    answer = ?,
                    sort_order = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $question,
                $answer,
                $sort_order,
                $status,
                $faq_id
            ]);

            $_SESSION['success_message'] = "FAQ updated successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Unable to save FAQ.";
    }

    header("Location: faq.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM faq_items ORDER BY sort_order ASC, id ASC");
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h1>FAQ Management</h1>
                <p>Manage common questions and answers</p>
            </div>

        </div>

        <div class="topbar-right">
            <div class="top-admin">
                <div class="top-admin-avatar">
                    <?= e($_SESSION['admin_name'] ?? 'Administrator') ?>
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

        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3><?= $edit_faq ? 'Edit FAQ' : 'Add New FAQ' ?></h3>
                </div>
            </div>

            <form method="POST" action="faq.php">
                <input type="hidden" name="action" value="<?= $edit_faq ? 'edit' : 'add' ?>">
                <?php if ($edit_faq): ?>
                    <input type="hidden" name="faq_id" value="<?= (int) ($edit_faq['id'] ?? 0) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" name="question" value="<?= e($edit_faq['question'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="<?= (int) ($edit_faq['sort_order'] ?? 0) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="status" <?= (!isset($edit_faq) || (int) ($edit_faq['status'] ?? 1) === 1) ? 'checked' : '' ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" name="answer" rows="6" required><?= e($edit_faq['answer'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_faq ? 'Update FAQ' : 'Add FAQ' ?>
                    </button>
                    <?php if ($edit_faq): ?>
                        <a href="faq.php" class="btn btn-outline-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-card mt-4">
            <div class="admin-card-header">
                <h3>FAQ List</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($faqs)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No FAQ items found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($faqs as $index => $faq): ?>
                                <tr>
                                    <td><?= (int) $index + 1 ?></td>
                                    <td><?= e($faq['question'] ?? '') ?></td>
                                    <td>
                                        <span class="badge <?= (int) ($faq['status'] ?? 1) === 1 ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= (int) ($faq['status'] ?? 1) === 1 ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="faq.php?edit=<?= (int) ($faq['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="faq.php?toggle=<?= (int) ($faq['id'] ?? 0) ?>" class="btn btn-sm btn-outline-warning">Toggle</a>
                                            <a href="faq.php?delete=<?= (int) ($faq['id'] ?? 0) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this FAQ?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</main>

<style>
    .content-body {
        padding: 30px 24px 40px;
        max-width: 100%;
        overflow-x: hidden;
    }

    .gs-admin-alert {
        margin-bottom: 18px;
    }

    .admin-card {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(8, 29, 48, 0.05);
        padding: 22px 20px;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }

    .admin-card-header {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf2f7;
    }

    .admin-card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #071a2b;
    }

    .form-label {
        font-weight: 600;
        color: #243447;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select,
    .form-check-input {
        border-radius: 12px;
        border: 1px solid #e8edf3;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: rgba(13, 110, 253, 0.45);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
    }

    textarea.form-control {
        min-height: 160px;
        resize: vertical;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        width: 100%;
        min-width: 680px;
        margin-bottom: 0;
    }

    .table th {
        color: #7b8794;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        background: #f8fafc;
        border-bottom: 1px solid #e8edf3;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
        color: #243447;
        border-color: #edf2f7;
        word-break: break-word;
    }

    .badge {
        font-size: 0.72rem;
        padding: 0.48rem 0.7rem;
        border-radius: 999px;
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .content-body {
            padding: 20px 14px 30px;
        }

        .admin-card {
            padding: 18px 14px;
        }

        .admin-card-header {
            margin-bottom: 14px;
        }

        .btn {
            width: 100%;
        }

        .mt-4.d-flex.gap-2 {
            flex-direction: column;
        }
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
