<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['admin_id']) ||
    (int) $_SESSION['admin_id'] <= 0 ||
    ($_SESSION['admin_role'] ?? '') !== 'super_admin'
) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$page_title = 'Create User | GS Tech Solutions';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 1;

    if ($name === '' || $email === '') {
        $_SESSION['error_message'] = 'Name and email are required.';
        header('Location: create-user.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Please enter a valid email address.';
        header('Location: create-user.php');
        exit;
    }

    if ($password === '') {
        $_SESSION['error_message'] = 'Password is required.';
        header('Location: create-user.php');
        exit;
    }

    $allowed_roles = ['user', 'admin', 'super_admin'];
    if (!in_array($role, $allowed_roles, true)) {
        $role = 'user';
    }

    try {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $_SESSION['error_message'] = 'A user with this email already exists.';
            header('Location: create-user.php');
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())'
        );

        $stmt->execute([$name, $email, $hashed_password, $role, $status]);

        $_SESSION['success_message'] = 'User created successfully.';
        header('Location: users.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Unable to create user. Please try again.';
        header('Location: create-user.php');
        exit;
    }
}

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="main">
    <header class="topbar">
        <div>
            <button type="button" class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="bi bi-list"></i>
            </button>

            <div class="page-heading">
                <h1>Create User</h1>
                <p>Add a new admin or user account</p>
            </div>
        </div>

        <div class="topbar-right">
            <div class="topbar-admin">
                <div class="topbar-avatar"><?php echo e(substr($admin_name, 0, 1)); ?></div>
                <div>
                    <strong><?php echo e($admin_name); ?></strong>
                    <small>Super Admin</small>
                </div>
            </div>
        </div>
    </header>

    <section class="content">
        <div class="page-header-row">
            <div>
                <span class="page-eyebrow">USER MANAGEMENT</span>
                <h2>Create New User</h2>
                <p>Register a new user account from the admin panel.</p>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo e($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo e($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="dashboard-card create-user-card">
            <div class="card-header-custom">
                <div>
                    <h3>User Details</h3>
                    <p>Fill in the information below to create a new user.</p>
                </div>
            </div>

            <div class="card-body-custom">
                <form method="POST" action="create-user.php" class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter full name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter email address" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter password" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select form-select-lg">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center gap-3 mt-2">
                        <a href="users.php" class="btn btn-light btn-lg">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Users
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<style>
    .page-header-row {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .page-eyebrow {
        display: block;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #0d6efd;
        margin-bottom: 6px;
    }

    .page-header-row h2 {
        margin: 0;
        font-size: 25px;
        font-weight: 800;
        color: #142235;
    }

    .page-header-row p {
        margin: 6px 0 0;
        color: #8793a3;
        font-size: 13px;
    }

    .topbar {
        min-height: 82px;
        background: #ffffff;
        border-bottom: 1px solid #edf0f4;
        padding: 0 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .page-heading h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #142235;
    }

    .page-heading p {
        margin: 3px 0 0;
        font-size: 11px;
        color: #929cab;
    }

    .mobile-menu-btn {
        display: none;
        border: 0;
        background: transparent;
        font-size: 24px;
        margin-right: 8px;
    }

    .topbar-admin {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .topbar-avatar {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0d6efd, #00b8d9);
        color: white;
        font-size: 14px;
        font-weight: 800;
    }

    .topbar-admin strong {
        display: block;
        font-size: 12px;
    }

    .topbar-admin small {
        color: #99a2af;
        font-size: 10px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(20,34,53,0.05);
    }

    .create-user-card {
        max-width: 980px;
        margin: 0 auto;
    }

    .card-header-custom {
        padding: 24px 28px 0;
    }

    .card-header-custom h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #142235;
    }

    .card-header-custom p {
        margin: 6px 0 0;
        color: #8793a3;
        font-size: 12px;
    }

    .card-body-custom {
        padding: 28px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border: 1px solid #dfe7f1;
        border-radius: 12px;
        background: #f8fbff;
        color: #142235;
        padding: 0.8rem 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #8ab4ff;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.10);
        background: #ffffff;
    }

    .btn-lg {
        border-radius: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .topbar {
            padding: 0 16px;
        }

        .mobile-menu-btn {
            display: inline-block;
        }

        .page-header-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
