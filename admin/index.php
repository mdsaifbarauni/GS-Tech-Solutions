<?php

// ==========================================================
// GS TECH SOLUTIONS
// ADMIN DASHBOARD
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================================
// ADMIN AUTH
// ==========================================================

if (
    !isset($_SESSION['admin_id']) ||
    (int) $_SESSION['admin_id'] <= 0 ||
    ($_SESSION['admin_role'] ?? '') !== 'super_admin'
) {
    header("Location: login.php");
    exit;
}


// ==========================================================
// DATABASE
// ==========================================================

require_once "../config/database.php";


// ==========================================================
// PAGE TITLE
// ==========================================================

$page_title = "Dashboard | GS Tech Solutions";


// ==========================================================
// ADMIN
// ==========================================================

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';


// ==========================================================
// COUNTS
// ==========================================================

function getCount(PDO $pdo, string $sql): int
{
    try {

        $stmt = $pdo->query($sql);

        return (int) $stmt->fetchColumn();

    } catch (PDOException $e) {

        return 0;

    }
}


$total_users = getCount(
    $pdo,
    "SELECT COUNT(*) FROM users"
);


$active_users = getCount(
    $pdo,
    "SELECT COUNT(*) FROM users WHERE status = 1"
);


$total_inquiries = getCount(
    $pdo,
    "SELECT COUNT(*) FROM contact_inquiries"
);


$pending_inquiries = getCount(
    $pdo,
    "SELECT COUNT(*)
     FROM contact_inquiries
     WHERE status = 'pending'"
);


$total_services = getCount(
    $pdo,
    "SELECT COUNT(*) FROM services"
);


// ==========================================================
// RECENT USERS
// ==========================================================

$recent_users = [];

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            email,
            role,
            status,
            created_at
        FROM users
        ORDER BY id DESC
        LIMIT 6
    ");

    $recent_users =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $recent_users = [];

}


// ==========================================================
// RECENT INQUIRIES
// ==========================================================

$recent_inquiries = [];

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            email,
            subject,
            status,
            created_at
        FROM contact_inquiries
        ORDER BY id DESC
        LIMIT 6
    ");

    $recent_inquiries =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $recent_inquiries = [];

}


// ==========================================================
// ADMIN HEADER
// ==========================================================

require_once "includes/header.php";


// ==========================================================
// SIDEBAR
// ==========================================================

require_once "includes/sidebar.php";

?>


<!-- ======================================================
     MAIN CONTENT
======================================================= -->

<main class="main">


    <!-- ==================================================
         TOPBAR
    =================================================== -->

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

                <h1>

                    Dashboard

                </h1>


                <p>

                    Manage your business from one place

                </p>

            </div>


        </div>


        <div class="topbar-right">


            <div class="top-admin">


                <div class="top-admin-avatar">

                    <?= e(
                        getInitials($admin_name)
                    ) ?>

                </div>


                <div class="top-admin-info">

                    <strong>

                        <?= e($admin_name) ?>

                    </strong>


                    <span>

                        Super Admin

                    </span>

                </div>


            </div>


        </div>


    </header>


    <!-- ==================================================
         CONTENT
    =================================================== -->

    <section class="content">


        <!-- =================================================
             WELCOME
        ================================================== -->

        <div
            class="welcome-card"
            style="
                position:relative;
                overflow:hidden;
                border-radius:18px;
                padding:27px 30px;
                margin-bottom:25px;
                color:white;
                background:
                linear-gradient(
                    110deg,
                    #0b2942,
                    #0d6efd
                );
            "
        >

            <h2 class="mb-1">

                Welcome back,
                <?= e($admin_name) ?> 👋

            </h2>


            <p class="mb-0">

                Here's what's happening with
                GS Tech Solutions today.

            </p>


            <small class="d-block mt-3">

                <i class="bi bi-calendar3 me-1"></i>

                <?= date('l, d F Y') ?>

            </small>

        </div>


        <!-- =================================================
             STATISTICS
        ================================================== -->

        <div class="row g-3 mb-4">


            <div class="col-xl-3 col-md-6">

                <div class="panel p-4 h-100">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total Users
                            </small>

                            <h3 class="mt-2 mb-1 fw-bold">

                                <?= number_format($total_users) ?>

                            </h3>

                            <a
                                href="users.php"
                                class="small"
                            >

                                Manage Users
                                <i class="bi bi-arrow-right"></i>

                            </a>

                        </div>


                        <div
                            class="
                                rounded-3
                                d-flex
                                align-items-center
                                justify-content-center
                            "
                            style="
                                width:48px;
                                height:48px;
                                background:#eaf2ff;
                                color:#0d6efd;
                            "
                        >

                            <i class="bi bi-people-fill fs-5"></i>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="panel p-4 h-100">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Active Users
                            </small>

                            <h3 class="mt-2 mb-1 fw-bold">

                                <?= number_format($active_users) ?>

                            </h3>

                            <a
                                href="users.php"
                                class="small"
                            >

                                View Users
                                <i class="bi bi-arrow-right"></i>

                            </a>

                        </div>


                        <div
                            class="
                                rounded-3
                                d-flex
                                align-items-center
                                justify-content-center
                            "
                            style="
                                width:48px;
                                height:48px;
                                background:#eaf8ef;
                                color:#16a34a;
                            "
                        >

                            <i class="bi bi-person-check-fill fs-5"></i>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="panel p-4 h-100">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total Inquiries
                            </small>

                            <h3 class="mt-2 mb-1 fw-bold">

                                <?= number_format($total_inquiries) ?>

                            </h3>

                            <a
                                href="inquiries.php"
                                class="small"
                            >

                                View Inquiries
                                <i class="bi bi-arrow-right"></i>

                            </a>

                        </div>


                        <div
                            class="
                                rounded-3
                                d-flex
                                align-items-center
                                justify-content-center
                            "
                            style="
                                width:48px;
                                height:48px;
                                background:#e8fbff;
                                color:#00a5c7;
                            "
                        >

                            <i class="bi bi-chat-square-text-fill fs-5"></i>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="panel p-4 h-100">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Pending Inquiries
                            </small>

                            <h3 class="mt-2 mb-1 fw-bold">

                                <?= number_format($pending_inquiries) ?>

                            </h3>

                            <a
                                href="inquiries.php?status=pending"
                                class="small"
                            >

                                Review Pending
                                <i class="bi bi-arrow-right"></i>

                            </a>

                        </div>


                        <div
                            class="
                                rounded-3
                                d-flex
                                align-items-center
                                justify-content-center
                            "
                            style="
                                width:48px;
                                height:48px;
                                background:#fff5df;
                                color:#f59e0b;
                            "
                        >

                            <i class="bi bi-clock-history fs-5"></i>

                        </div>

                    </div>

                </div>

            </div>


        </div>


        <!-- =================================================
             RECENT USERS
        ================================================== -->

        <div class="row g-4 mb-4">


            <div class="col-xl-8">


                <div class="panel">


                    <div class="panel-header">

                        <strong>

                            Recent Users

                        </strong>


                        <a
                            href="users.php"
                            class="small"
                        >

                            View All
                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>


                    <?php if (!empty($recent_users)): ?>


                        <div class="table-responsive">


                            <table class="table mb-0">

                                <thead>

                                    <tr>

                                        <th>User</th>

                                        <th>Role</th>

                                        <th>Status</th>

                                        <th>Joined</th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php foreach (
                                    $recent_users
                                    as $user
                                ): ?>

                                    <tr>


                                        <td>

                                            <strong>

                                                <?= e(
                                                    $user['name'] ??
                                                    'Unknown'
                                                ) ?>

                                            </strong>

                                            <br>

                                            <small class="text-muted">

                                                <?= e(
                                                    $user['email'] ??
                                                    ''
                                                ) ?>

                                            </small>

                                        </td>


                                        <td>

                                            <?= e(
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $user['role'] ??
                                                        'user'
                                                    )
                                                )
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php if (
                                                (int) (
                                                    $user['status'] ??
                                                    0
                                                ) === 1
                                            ): ?>

                                                <span
                                                    class="
                                                        badge
                                                        text-bg-success
                                                    "
                                                >

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="
                                                        badge
                                                        text-bg-danger
                                                    "
                                                >

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?= e(
                                                !empty(
                                                    $user['created_at']
                                                )
                                                    ? date(
                                                        'd M Y',
                                                        strtotime(
                                                            $user['created_at']
                                                        )
                                                    )
                                                    : '-'
                                            ) ?>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>


                                </tbody>

                            </table>


                        </div>


                    <?php else: ?>


                        <div class="p-5 text-center text-muted">

                            <i
                                class="
                                    bi bi-people
                                    fs-3
                                    d-block
                                    mb-2
                                "
                            ></i>

                            No users found.

                        </div>


                    <?php endif; ?>


                </div>


            </div>


            <!-- =================================================
                 QUICK ACTIONS
            ================================================== -->

            <div class="col-xl-4">


                <div class="panel">


                    <div class="panel-header">

                        <strong>

                            Quick Actions

                        </strong>

                    </div>


                    <div class="p-3">


                        <a
                            href="users.php"
                            class="
                                d-flex
                                align-items-center
                                gap-3
                                p-3
                                border-bottom
                                text-dark
                            "
                        >

                            <i class="bi bi-people fs-5"></i>

                            <span>

                                Manage Users

                            </span>

                            <i
                                class="
                                    bi
                                    bi-chevron-right
                                    ms-auto
                                "
                            ></i>

                        </a>


                        <a
                            href="services.php"
                            class="
                                d-flex
                                align-items-center
                                gap-3
                                p-3
                                border-bottom
                                text-dark
                            "
                        >

                            <i class="bi bi-briefcase fs-5"></i>

                            <span>

                                Manage Services

                            </span>

                            <i
                                class="
                                    bi
                                    bi-chevron-right
                                    ms-auto
                                "
                            ></i>

                        </a>


                        <a
                            href="inquiries.php"
                            class="
                                d-flex
                                align-items-center
                                gap-3
                                p-3
                                border-bottom
                                text-dark
                            "
                        >

                            <i class="bi bi-envelope fs-5"></i>

                            <span>

                                Contact Inquiries

                            </span>

                            <i
                                class="
                                    bi
                                    bi-chevron-right
                                    ms-auto
                                "
                            ></i>

                        </a>


                        <a
                            href="../index.php"
                            target="_blank"
                            class="
                                d-flex
                                align-items-center
                                gap-3
                                p-3
                                text-dark
                            "
                        >

                            <i class="bi bi-globe2 fs-5"></i>

                            <span>

                                View Website

                            </span>

                            <i
                                class="
                                    bi
                                    bi-box-arrow-up-right
                                    ms-auto
                                "
                            ></i>

                        </a>


                    </div>


                </div>


            </div>


        </div>


        <!-- =================================================
             RECENT INQUIRIES
        ================================================== -->

        <div class="panel">


            <div class="panel-header">

                <strong>

                    Recent Contact Inquiries

                </strong>


                <a
                    href="inquiries.php"
                    class="small"
                >

                    View All
                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>


            <?php if (!empty($recent_inquiries)): ?>


                <div class="table-responsive">


                    <table class="table mb-0">

                        <thead>

                            <tr>

                                <th>Name</th>

                                <th>Email</th>

                                <th>Subject</th>

                                <th>Status</th>

                                <th>Date</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach (
                            $recent_inquiries
                            as $inquiry
                        ): ?>


                            <tr>

                                <td>

                                    <?= e(
                                        $inquiry['name'] ??
                                        'Unknown'
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $inquiry['email'] ??
                                        ''
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $inquiry['subject'] ??
                                        'No Subject'
                                    ) ?>

                                </td>


                                <td>

                                    <?php
                                    $status =
                                        strtolower(
                                            $inquiry['status'] ??
                                            'pending'
                                        );
                                    ?>


                                    <?php if (
                                        $status === 'pending'
                                    ): ?>

                                        <span
                                            class="
                                                badge
                                                text-bg-warning
                                            "
                                        >

                                            Pending

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="
                                                badge
                                                text-bg-primary
                                            "
                                        >

                                            <?= e(
                                                ucfirst($status)
                                            ) ?>

                                        </span>

                                    <?php endif; ?>


                                </td>


                                <td>

                                    <?= e(
                                        !empty(
                                            $inquiry['created_at']
                                        )
                                            ? date(
                                                'd M Y',
                                                strtotime(
                                                    $inquiry['created_at']
                                                )
                                            )
                                            : '-'
                                    ) ?>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>


                </div>


            <?php else: ?>


                <div class="p-5 text-center text-muted">

                    <i
                        class="
                            bi bi-chat-square-text
                            fs-3
                            d-block
                            mb-2
                        "
                    ></i>

                    No contact inquiries found.

                </div>


            <?php endif; ?>


        </div>


<?php

// ==========================================================
// FOOTER
// ==========================================================

require_once "includes/footer.php";

?>