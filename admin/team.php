<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - Team Management
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

$page_title = "Team Management | GS Tech Solutions";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS team_members (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            designation VARCHAR(150) NULL,
            phone VARCHAR(20) NULL,
            email VARCHAR(150) NULL,
            bio TEXT NULL,
            profile_image VARCHAR(255) NULL,
            linkedin_url VARCHAR(255) NULL,
            facebook_url VARCHAR(255) NULL,
            instagram_url VARCHAR(255) NULL,
            status TINYINT(1) NULL DEFAULT 1,
            sort_order INT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Unable to initialize team members table.";
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['success_message'] = "Team member deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to delete team member.";
        }
    }

    header("Location: team.php");
    exit;
}

if (isset($_GET['toggle'])) {
    $toggle_id = (int) $_GET['toggle'];

    if ($toggle_id > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE team_members
                SET status = IF(status = 1, 0, 1)
                WHERE id = ?
            ");
            $stmt->execute([$toggle_id]);
            $_SESSION['success_message'] = "Team member status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Unable to update team member status.";
        }
    }

    header("Location: team.php");
    exit;
}

$edit_member = null;

if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];

    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_id]);
            $edit_member = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $edit_member = null;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $member_id = (int) ($_POST['member_id'] ?? 0);

    $name = trim($_POST['name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profile_image = trim($_POST['profile_image'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');
    $facebook_url = trim($_POST['facebook_url'] ?? '');
    $instagram_url = trim($_POST['instagram_url'] ?? '');
    $sort_order = (int) ($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;

    if ($name === '' || $designation === '') {
        $_SESSION['error_message'] = "Name and designation are required.";
        header("Location: team.php");
        exit;
    }

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("
                INSERT INTO team_members (
                    name,
                    designation,
                    phone,
                    email,
                    bio,
                    profile_image,
                    linkedin_url,
                    facebook_url,
                    instagram_url,
                    status,
                    sort_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $designation,
                $phone,
                $email,
                $bio,
                $profile_image,
                $linkedin_url,
                $facebook_url,
                $instagram_url,
                $status,
                $sort_order
            ]);

            $_SESSION['success_message'] = "Team member added successfully.";
        } elseif ($action === 'edit' && $member_id > 0) {
            $stmt = $pdo->prepare("
                UPDATE team_members
                SET
                    name = ?,
                    designation = ?,
                    phone = ?,
                    email = ?,
                    bio = ?,
                    profile_image = ?,
                    linkedin_url = ?,
                    facebook_url = ?,
                    instagram_url = ?,
                    status = ?,
                    sort_order = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $designation,
                $phone,
                $email,
                $bio,
                $profile_image,
                $linkedin_url,
                $facebook_url,
                $instagram_url,
                $status,
                $sort_order,
                $member_id
            ]);

            $_SESSION['success_message'] = "Team member updated successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Unable to save team member.";
    }

    header("Location: team.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC");
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h1>Team Management</h1>
                <p>Manage founder and team profiles</p>
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
                    <h3><?= $edit_member ? 'Edit Team Member' : 'Add New Team Member' ?></h3>
                </div>
            </div>

            <form method="POST" action="team.php">
                <input type="hidden" name="action" value="<?= $edit_member ? 'edit' : 'add' ?>">
                <?php if ($edit_member): ?>
                    <input type="hidden" name="member_id" value="<?= (int) ($edit_member['id'] ?? 0) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="<?= e($edit_member['name'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" class="form-control" name="designation" value="<?= e($edit_member['designation'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= e($edit_member['phone'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= e($edit_member['email'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Image Path</label>
                        <input type="text" class="form-control" name="profile_image" value="<?= e($edit_member['profile_image'] ?? 'assets/img/person/person-m-13.webp') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="<?= (int) ($edit_member['sort_order'] ?? 0) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="status" <?= (!isset($edit_member) || (int) ($edit_member['status'] ?? 1) === 1) ? 'checked' : '' ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">LinkedIn URL</label>
                        <input type="text" class="form-control" name="linkedin_url" value="<?= e($edit_member['linkedin_url'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Facebook URL</label>
                        <input type="text" class="form-control" name="facebook_url" value="<?= e($edit_member['facebook_url'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" class="form-control" name="instagram_url" value="<?= e($edit_member['instagram_url'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" rows="4"><?= e($edit_member['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_member ? 'Update Member' : 'Add Member' ?>
                    </button>
                    <?php if ($edit_member): ?>
                        <a href="team.php" class="btn btn-outline-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-card mt-4">
            <div class="admin-card-header">
                <h3>Team Members</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($team_members)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No team members found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($team_members as $index => $member): ?>
                                <tr>
                                    <td><?= (int) $index + 1 ?></td>
                                    <td><?= e($member['name'] ?? '') ?></td>
                                    <td><?= e($member['designation'] ?? '') ?></td>
                                    <td><?= e($member['phone'] ?? '-') ?></td>
                                    <td><?= e($member['email'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= (int) ($member['status'] ?? 1) === 1 ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= (int) ($member['status'] ?? 1) === 1 ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="team.php?edit=<?= (int) ($member['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="team.php?toggle=<?= (int) ($member['id'] ?? 0) ?>" class="btn btn-sm btn-outline-warning">Toggle</a>
                                            <a href="team.php?delete=<?= (int) ($member['id'] ?? 0) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this team member?')">Delete</a>
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
        min-height: 140px;
        resize: vertical;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        width: 100%;
        min-width: 760px;
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
