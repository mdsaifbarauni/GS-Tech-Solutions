<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - Users Management
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

$page_title = "Users Management | GS Tech Solutions";


// ==========================================================
// Helper
// ==========================================================

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


// ==========================================================
// Delete User
// ==========================================================

if (isset($_GET['delete'])) {

    $delete_id = (int) $_GET['delete'];

    // Prevent deleting yourself
    if ($delete_id === (int) $_SESSION['admin_id']) {

        $_SESSION['error_message'] = "You cannot delete your own account.";

    } else {

        try {

            $stmt = $pdo->prepare("
                DELETE FROM users
                WHERE id = ?
            ");

            $stmt->execute([$delete_id]);

            $_SESSION['success_message'] = "User deleted successfully.";

        } catch (PDOException $e) {

            $_SESSION['error_message'] = "Unable to delete user.";
        }
    }

    header("Location: users.php");
    exit;
}


// ==========================================================
// Toggle User Status
// ==========================================================

if (isset($_GET['toggle'])) {

    $toggle_id = (int) $_GET['toggle'];

    if ($toggle_id === (int) $_SESSION['admin_id']) {

        $_SESSION['error_message'] = "You cannot deactivate your own account.";

    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE users
                SET status = IF(status = 1, 0, 1)
                WHERE id = ?
            ");

            $stmt->execute([$toggle_id]);

            $_SESSION['success_message'] = "User status updated successfully.";

        } catch (PDOException $e) {

            $_SESSION['error_message'] = "Unable to update user status.";
        }
    }

    header("Location: users.php");
    exit;
}


// ==========================================================
// Add / Edit User
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    $user_id = (int) ($_POST['user_id'] ?? 0);

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 1;


    // Validation

    if ($name === '' || $email === '') {

        $_SESSION['error_message'] = "Name and email are required.";

        header("Location: users.php");
        exit;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_SESSION['error_message'] = "Please enter a valid email address.";

        header("Location: users.php");
        exit;
    }


    $allowed_roles = [
        'user',
        'admin',
        'super_admin'
    ];

    if (!in_array($role, $allowed_roles, true)) {
        $role = 'user';
    }


    try {

        // --------------------------------------------------
        // ADD USER
        // --------------------------------------------------

        if ($action === 'add') {

            if ($password === '') {

                $_SESSION['error_message'] = "Password is required.";

                header("Location: users.php");
                exit;
            }


            // Check duplicate email

            $check = $pdo->prepare("
                SELECT id
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $check->execute([$email]);

            if ($check->fetch()) {

                $_SESSION['error_message'] =
                    "A user with this email already exists.";

                header("Location: users.php");
                exit;
            }


            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    role,
                    status,
                    created_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )
            ");

            $stmt->execute([
                $name,
                $email,
                $hashed_password,
                $role,
                $status
            ]);


            $_SESSION['success_message'] =
                "User added successfully.";
        }


        // --------------------------------------------------
        // EDIT USER
        // --------------------------------------------------

        elseif ($action === 'edit') {

            if ($user_id <= 0) {

                $_SESSION['error_message'] =
                    "Invalid user.";

                header("Location: users.php");
                exit;
            }


            // Check duplicate email except current user

            $check = $pdo->prepare("
                SELECT id
                FROM users
                WHERE email = ?
                AND id != ?
                LIMIT 1
            ");

            $check->execute([
                $email,
                $user_id
            ]);


            if ($check->fetch()) {

                $_SESSION['error_message'] =
                    "Another user already uses this email.";

                header("Location: users.php");
                exit;
            }


            if ($password !== '') {

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                $stmt = $pdo->prepare("
                    UPDATE users
                    SET
                        name = ?,
                        email = ?,
                        password = ?,
                        role = ?,
                        status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $hashed_password,
                    $role,
                    $status,
                    $user_id
                ]);

            } else {

                $stmt = $pdo->prepare("
                    UPDATE users
                    SET
                        name = ?,
                        email = ?,
                        role = ?,
                        status = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $role,
                    $status,
                    $user_id
                ]);
            }


            // Update current session name/email
            if ($user_id === (int) $_SESSION['admin_id']) {

                $_SESSION['admin_name'] = $name;
                $_SESSION['admin_email'] = $email;
            }


            $_SESSION['success_message'] =
                "User updated successfully.";
        }


    } catch (PDOException $e) {

        $_SESSION['error_message'] =
            "Database error. Please try again.";
    }


    header("Location: users.php");
    exit;
}


// ==========================================================
// Search / Filters
// ==========================================================

$search = trim($_GET['search'] ?? '');

$role_filter = trim($_GET['role'] ?? '');

$status_filter = $_GET['status'] ?? '';

$where = [];

$params = [];


// Search

if ($search !== '') {

    $where[] = "
        (
            name LIKE ?
            OR email LIKE ?
        )
    ";

    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}


// Role

if ($role_filter !== '') {

    $where[] = "role = ?";

    $params[] = $role_filter;
}


// Status

if ($status_filter !== '') {

    $where[] = "status = ?";

    $params[] = (int) $status_filter;
}


$where_sql = '';

if (!empty($where)) {

    $where_sql = 'WHERE ' . implode(' AND ', $where);
}


// ==========================================================
// Get Users
// ==========================================================

$stmt = $pdo->prepare("
    SELECT
        id,
        name,
        email,
        role,
        status,
        created_at
    FROM users
    {$where_sql}
    ORDER BY id DESC
");

$stmt->execute($params);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================================================
// Statistics
// ==========================================================

$total_users = (int) $pdo
    ->query("SELECT COUNT(*) FROM users")
    ->fetchColumn();

$active_users = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM users
        WHERE status = 1
    ")
    ->fetchColumn();

$inactive_users = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM users
        WHERE status = 0
    ")
    ->fetchColumn();

$admin_users = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM users
        WHERE role IN ('admin', 'super_admin')
    ")
    ->fetchColumn();


// ==========================================================
// Flash Messages
// ==========================================================

$success_message = $_SESSION['success_message'] ?? '';

$error_message = $_SESSION['error_message'] ?? '';

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);


// ==========================================================
// Admin Header
// ==========================================================

require_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/includes/sidebar.php';

?>

<main class="main">

    <!-- ======================================================
         TOPBAR
    ======================================================= -->

    <header class="topbar">

        <div>

            <button
                type="button"
                class="mobile-menu-btn"
                id="mobileMenuBtn"
            >
                <i class="bi bi-list"></i>
            </button>

            <div class="page-heading">

                <h1>Users</h1>

                <p>
                    Manage system users and administrators
                </p>

            </div>

        </div>


        <div class="topbar-right">

            <div class="topbar-admin">

                <div class="topbar-avatar">
                    <?= e(substr($admin_name, 0, 1)); ?>
                </div>

                <div>

                    <strong>
                        <?= e($admin_name); ?>
                    </strong>

                    <small>
                        Super Admin
                    </small>

                </div>

            </div>

        </div>

    </header>


    <!-- ======================================================
         CONTENT
    ======================================================= -->

    <section class="content">

        <!-- Page Header -->

        <div class="page-header-row">

            <div>

                <span class="page-eyebrow">
                    USER MANAGEMENT
                </span>

                <h2>
                    All Users
                </h2>

                <p>
                    Create, update and manage users.
                </p>

            </div>


            <div class="d-flex gap-2 flex-wrap">
                <a
                    href="create-user.php"
                    class="btn btn-primary add-user-btn"
                >
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Create User
                </a>

                <button
                    type="button"
                    class="btn btn-outline-primary add-user-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#userModal"
                    onclick="openAddUserModal()"
                >
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    Quick Add
                </button>
            </div>

        </div>


        <!-- Flash Messages -->

        <?php if ($success_message): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >

                <i class="bi bi-check-circle-fill me-2"></i>

                <?= e($success_message); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <?php if ($error_message): ?>

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <?= e($error_message); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- ==================================================
             STATISTICS
        =================================================== -->

        <div class="row g-4 mb-4">

            <div class="col-xl-3 col-md-6">

                <div class="user-stat-card">

                    <div class="stat-icon blue">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div>
                        <span>Total Users</span>
                        <strong>
                            <?= number_format($total_users); ?>
                        </strong>
                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="user-stat-card">

                    <div class="stat-icon green">
                        <i class="bi bi-person-check-fill"></i>
                    </div>

                    <div>
                        <span>Active Users</span>
                        <strong>
                            <?= number_format($active_users); ?>
                        </strong>
                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="user-stat-card">

                    <div class="stat-icon orange">
                        <i class="bi bi-person-x-fill"></i>
                    </div>

                    <div>
                        <span>Inactive Users</span>
                        <strong>
                            <?= number_format($inactive_users); ?>
                        </strong>
                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="user-stat-card">

                    <div class="stat-icon purple">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <div>
                        <span>Administrators</span>
                        <strong>
                            <?= number_format($admin_users); ?>
                        </strong>
                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FILTER CARD
        =================================================== -->

        <div class="dashboard-card mb-4">

            <div class="card-body-custom">

                <form
                    method="GET"
                    action="users.php"
                    class="row g-3 align-items-end"
                >

                    <div class="col-lg-5">

                        <label class="form-label">
                            Search User
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search by name or email..."
                                value="<?= e($search); ?>"
                            >

                        </div>

                    </div>


                    <div class="col-lg-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select
                            name="role"
                            class="form-select"
                        >

                            <option value="">
                                All Roles
                            </option>

                            <option
                                value="user"
                                <?= $role_filter === 'user' ? 'selected' : ''; ?>
                            >
                                User
                            </option>

                            <option
                                value="admin"
                                <?= $role_filter === 'admin' ? 'selected' : ''; ?>
                            >
                                Admin
                            </option>

                            <option
                                value="super_admin"
                                <?= $role_filter === 'super_admin' ? 'selected' : ''; ?>
                            >
                                Super Admin
                            </option>

                        </select>

                    </div>


                    <div class="col-lg-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="1"
                                <?= $status_filter === '1' ? 'selected' : ''; ?>
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                <?= $status_filter === '0' ? 'selected' : ''; ?>
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-lg-2">

                        <button
                            type="submit"
                            class="btn btn-dark w-100"
                        >

                            <i class="bi bi-funnel-fill me-1"></i>

                            Filter

                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- ==================================================
             USERS TABLE
        =================================================== -->

        <div class="dashboard-card">

            <div class="card-header-custom">

                <div>

                    <h3>
                        User List
                    </h3>

                    <p>
                        <?= number_format(count($users)); ?>
                        users found
                    </p>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table users-table align-middle mb-0">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                User
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Joined
                            </th>

                            <th class="text-end">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (!empty($users)): ?>

                        <?php foreach ($users as $user): ?>

                            <?php

                            $initial = strtoupper(
                                substr(
                                    trim($user['name']),
                                    0,
                                    1
                                )
                            );

                            ?>

                            <tr>

                                <td>
                                    <span class="user-id">
                                        #<?= (int) $user['id']; ?>
                                    </span>
                                </td>


                                <td>

                                    <div class="user-cell">

                                        <div class="user-avatar">
                                            <?= e($initial); ?>
                                        </div>

                                        <div>

                                            <strong>
                                                <?= e($user['name']); ?>
                                            </strong>

                                            <?php if (
                                                (int) $user['id']
                                                ===
                                                (int) $_SESSION['admin_id']
                                            ): ?>

                                                <span class="you-badge">
                                                    YOU
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="email-text">
                                        <?= e($user['email']); ?>
                                    </span>

                                </td>


                                <td>

                                    <?php

                                    $role_class = match ($user['role']) {

                                        'super_admin' => 'role-super',

                                        'admin' => 'role-admin',

                                        default => 'role-user'
                                    };

                                    ?>

                                    <span
                                        class="role-badge <?= $role_class; ?>"
                                    >

                                        <?php if ($user['role'] === 'super_admin'): ?>

                                            <i class="bi bi-shield-fill-check"></i>
                                            Super Admin

                                        <?php elseif ($user['role'] === 'admin'): ?>

                                            <i class="bi bi-shield-fill"></i>
                                            Admin

                                        <?php else: ?>

                                            <i class="bi bi-person-fill"></i>
                                            User

                                        <?php endif; ?>

                                    </span>

                                </td>


                                <td>

                                    <?php if ((int) $user['status'] === 1): ?>

                                        <span class="status-badge active">
                                            <i class="bi bi-circle-fill"></i>
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="status-badge inactive">
                                            <i class="bi bi-circle-fill"></i>
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <span class="date-text">

                                        <?= date(
                                            'd M Y',
                                            strtotime($user['created_at'])
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <div class="action-buttons">

                                        <!-- Edit -->

                                        <button
                                            type="button"
                                            class="action-btn edit"
                                            title="Edit User"
                                            data-bs-toggle="modal"
                                            data-bs-target="#userModal"
                                            onclick='openEditUserModal(
                                                <?= json_encode($user, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
                                            )'
                                        >

                                            <i class="bi bi-pencil-fill"></i>

                                        </button>


                                        <!-- Toggle -->

                                        <?php if (
                                            (int) $user['id']
                                            !==
                                            (int) $_SESSION['admin_id']
                                        ): ?>

                                            <a
                                                href="users.php?toggle=<?= (int) $user['id']; ?>"
                                                class="action-btn status"
                                                title="<?= (int) $user['status'] === 1 ? 'Deactivate' : 'Activate'; ?>"
                                                onclick="return confirm('Are you sure you want to change this user status?');"
                                            >

                                                <?php if ((int) $user['status'] === 1): ?>

                                                    <i class="bi bi-person-dash-fill"></i>

                                                <?php else: ?>

                                                    <i class="bi bi-person-check-fill"></i>

                                                <?php endif; ?>

                                            </a>

                                        <?php endif; ?>


                                        <!-- Delete -->

                                        <?php if (
                                            (int) $user['id']
                                            !==
                                            (int) $_SESSION['admin_id']
                                        ): ?>

                                            <a
                                                href="users.php?delete=<?= (int) $user['id']; ?>"
                                                class="action-btn delete"
                                                title="Delete User"
                                                onclick="return confirm('Are you sure you want to permanently delete this user?');"
                                            >

                                                <i class="bi bi-trash3-fill"></i>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="empty-state"
                            >

                                <div>

                                    <i class="bi bi-people"></i>

                                    <h4>
                                        No users found
                                    </h4>

                                    <p>
                                        Try changing your search or filters.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</main>


<!-- ==========================================================
     ADD / EDIT USER MODAL
========================================================== -->

<div
    class="modal fade"
    id="userModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content user-modal">

            <div class="modal-header">

                <div>

                    <span class="modal-eyebrow">
                        USER MANAGEMENT
                    </span>

                    <h5
                        class="modal-title"
                        id="modalTitle"
                    >
                        Add New User
                    </h5>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <form
                method="POST"
                action="users.php"
            >

                <input
                    type="hidden"
                    name="action"
                    id="formAction"
                    value="add"
                >

                <input
                    type="hidden"
                    name="user_id"
                    id="userId"
                    value="0"
                >


                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="userName"
                            class="form-control"
                            placeholder="Enter full name"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="userEmail"
                            class="form-control"
                            placeholder="Enter email address"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">

                            Password

                            <small id="passwordHint">
                                Required
                            </small>

                        </label>

                        <input
                            type="password"
                            name="password"
                            id="userPassword"
                            class="form-control"
                            placeholder="Enter password"
                        >

                    </div>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Role
                            </label>

                            <select
                                name="role"
                                id="userRole"
                                class="form-select"
                            >

                                <option value="user">
                                    User
                                </option>

                                <option value="admin">
                                    Admin
                                </option>

                                <option value="super_admin">
                                    Super Admin
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                id="userStatus"
                                class="form-select"
                            >

                                <option value="1">
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveUserBtn"
                    >

                        <i class="bi bi-check2-circle me-1"></i>

                        Save User

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<style>

/* ==========================================================
   USERS PAGE
========================================================== */

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


/* ==========================================================
   TOPBAR
========================================================== */

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

    background: linear-gradient(
        135deg,
        #0d6efd,
        #00b8d9
    );

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


/* ==========================================================
   STAT CARDS
========================================================== */

.user-stat-card {
    background: #ffffff;
    border: 1px solid #edf0f4;
    border-radius: 16px;
    padding: 20px;

    display: flex;
    align-items: center;
    gap: 15px;

    box-shadow: 0 5px 20px rgba(20,34,53,0.04);
}

.stat-icon {
    width: 50px;
    height: 50px;

    border-radius: 13px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 20px;
}

.stat-icon.blue {
    background: #eaf2ff;
    color: #0d6efd;
}

.stat-icon.green {
    background: #e9fbf1;
    color: #20a464;
}

.stat-icon.orange {
    background: #fff4e7;
    color: #f08a24;
}

.stat-icon.purple {
    background: #f2ecff;
    color: #7952d6;
}

.user-stat-card span {
    display: block;
    font-size: 11px;
    color: #8e99a8;
    margin-bottom: 4px;
}

.user-stat-card strong {
    display: block;
    font-size: 24px;
    color: #142235;
    line-height: 1;
}


/* ==========================================================
   CARD
========================================================== */

.dashboard-card {
    background: #ffffff;
    border: 1px solid #edf0f4;
    border-radius: 16px;
    overflow: hidden;

    box-shadow: 0 5px 20px rgba(20,34,53,0.04);
}

.card-body-custom {
    padding: 22px;
}

.card-header-custom {
    padding: 21px 23px;

    border-bottom: 1px solid #edf0f4;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-custom h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
}

.card-header-custom p {
    margin: 4px 0 0;
    color: #98a1ae;
    font-size: 11px;
}


/* ==========================================================
   FORM
========================================================== */

.form-label {
    font-size: 12px;
    font-weight: 700;
    color: #354052;
    margin-bottom: 7px;
}

.form-control,
.form-select {
    min-height: 43px;
    border-color: #e3e8ee;
    border-radius: 9px;
    font-size: 12px;
}

.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.08);
}

.input-group-text {
    background: #ffffff;
    border-color: #e3e8ee;
    color: #9aa4b1;
}


/* ==========================================================
   TABLE
========================================================== */

.users-table {
    font-size: 12px;
}

.users-table thead th {
    background: #fafbfd;
    color: #7d8998;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 0.5px;

    text-transform: uppercase;

    padding: 15px 20px;

    border-bottom: 1px solid #edf0f4;
}

.users-table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2f5;
    color: #4d5867;
}

.users-table tbody tr:hover {
    background: #fafcff;
}

.user-id {
    color: #9ba5b2;
    font-size: 11px;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 11px;
}

.user-avatar {
    width: 37px;
    height: 37px;

    border-radius: 10px;

    background: #eaf2ff;
    color: #0d6efd;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 13px;
    font-weight: 800;
}

.user-cell strong {
    font-size: 12px;
    color: #263447;
}

.you-badge {
    margin-left: 6px;
    padding: 2px 5px;

    border-radius: 4px;

    font-size: 8px;
    font-weight: 800;

    color: #0d6efd;
    background: #eaf2ff;
}

.email-text {
    color: #687586;
}

.date-text {
    color: #8b96a4;
}


/* ==========================================================
   ROLE BADGES
========================================================== */

.role-badge,
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    padding: 5px 9px;

    border-radius: 7px;

    font-size: 10px;
    font-weight: 700;
}

.role-super {
    color: #6845c5;
    background: #f0eaff;
}

.role-admin {
    color: #0879a4;
    background: #e7f7fc;
}

.role-user {
    color: #506070;
    background: #f0f3f6;
}


/* ==========================================================
   STATUS
========================================================== */

.status-badge.active {
    color: #15834d;
    background: #eaf9f1;
}

.status-badge.inactive {
    color: #9a5c12;
    background: #fff3df;
}

.status-badge i {
    font-size: 6px;
}


/* ==========================================================
   ACTION BUTTONS
========================================================== */

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 6px;
}

.action-btn {
    width: 32px;
    height: 32px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    text-decoration: none;
    border: 0;

    transition: 0.2s ease;
}

.action-btn.edit {
    color: #0d6efd;
    background: #eaf2ff;
}

.action-btn.status {
    color: #d48415;
    background: #fff4e5;
}

.action-btn.delete {
    color: #dc3545;
    background: #ffedf0;
}

.action-btn:hover {
    transform: translateY(-2px);
    filter: brightness(0.96);
}


/* ==========================================================
   EMPTY
========================================================== */

.empty-state {
    height: 280px;
    text-align: center;
}

.empty-state > div {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.empty-state i {
    font-size: 45px;
    color: #c9d0d9;
}

.empty-state h4 {
    margin: 12px 0 4px;
    font-size: 15px;
}

.empty-state p {
    margin: 0;
    color: #9ba5b1;
    font-size: 11px;
}


/* ==========================================================
   MODAL
========================================================== */

.user-modal {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
}

.user-modal .modal-header {
    padding: 22px 24px;
    border-bottom: 1px solid #edf0f4;
}

.modal-eyebrow {
    display: block;
    color: #0d6efd;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.2px;
    margin-bottom: 5px;
}

.user-modal .modal-title {
    font-size: 19px;
    font-weight: 800;
}

.user-modal .modal-body {
    padding: 24px;
}

.user-modal .modal-footer {
    padding: 17px 24px;
    border-top: 1px solid #edf0f4;
}

#passwordHint {
    color: #9aa4b1;
    font-weight: 500;
    margin-left: 4px;
}


/* ==========================================================
   RESPONSIVE
========================================================== */

@media (max-width: 991.98px) {

    .main {
        margin-left: 0 !important;
    }

    .mobile-menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .topbar {
        padding: 0 18px;
    }

    .topbar-admin > div:last-child {
        display: none;
    }

    .page-header-row {
        align-items: flex-start;
        flex-direction: column;
    }

    .add-user-btn {
        width: 100%;
    }

}

@media (max-width: 575.98px) {

    .content {
        padding: 18px !important;
    }

    .topbar {
        min-height: 70px;
    }

    .user-stat-card {
        padding: 16px;
    }

    .users-table {
        min-width: 900px;
    }

}

</style>


<script>

// ==========================================================
// USERS MODAL
// ==========================================================

function openAddUserModal() {

    document.getElementById('modalTitle').textContent =
        'Add New User';

    document.getElementById('formAction').value =
        'add';

    document.getElementById('userId').value =
        '0';

    document.getElementById('userName').value =
        '';

    document.getElementById('userEmail').value =
        '';

    document.getElementById('userPassword').value =
        '';

    document.getElementById('userRole').value =
        'user';

    document.getElementById('userStatus').value =
        '1';

    document.getElementById('passwordHint').textContent =
        'Required';

    document.getElementById('saveUserBtn').innerHTML =
        '<i class="bi bi-check2-circle me-1"></i> Save User';
}


// ==========================================================
// EDIT USER
// ==========================================================

function openEditUserModal(user) {

    document.getElementById('modalTitle').textContent =
        'Edit User';

    document.getElementById('formAction').value =
        'edit';

    document.getElementById('userId').value =
        user.id;

    document.getElementById('userName').value =
        user.name;

    document.getElementById('userEmail').value =
        user.email;

    document.getElementById('userPassword').value =
        '';

    document.getElementById('userRole').value =
        user.role;

    document.getElementById('userStatus').value =
        user.status;

    document.getElementById('passwordHint').textContent =
        'Leave blank to keep current password';

    document.getElementById('saveUserBtn').innerHTML =
        '<i class="bi bi-check2-circle me-1"></i> Update User';
}

</script>


<?php

// ==========================================================
// ADMIN FOOTER
// ==========================================================

require_once __DIR__ . '/includes/footer.php';

?>