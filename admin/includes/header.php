<?php

// ==========================================================
// GS TECH SOLUTIONS
// ADMIN HEADER
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================================
// ADMIN AUTH CHECK
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
// ADMIN DATA
// ==========================================================

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';


// ==========================================================
// PAGE TITLE
// ==========================================================

$page_title = $page_title ?? 'Admin Dashboard | GS Tech Solutions';


// ==========================================================
// ACTIVE PAGE
// ==========================================================

$current_page = basename($_SERVER['PHP_SELF']);


// ==========================================================
// ESCAPE FUNCTION
// ==========================================================

if (!function_exists('e')) {

    function e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


// ==========================================================
// INITIALS
// ==========================================================

if (!function_exists('getInitials')) {

    function getInitials($name): string
    {
        $name = trim($name);

        if ($name === '') {
            return 'A';
        }

        $parts = preg_split('/\s+/', $name);

        if (count($parts) >= 2) {

            return strtoupper(
                substr($parts[0], 0, 1) .
                substr($parts[1], 0, 1)
            );
        }

        return strtoupper(
            substr($name, 0, 2)
        );
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="GS Tech Solutions Administration Panel"
    >

    <title><?= e($page_title) ?></title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >


    <style>

        :root {

            --gs-primary: #0d6efd;
            --gs-secondary: #00b8d9;

            --gs-dark: #071a2b;
            --gs-sidebar: #081d30;

            --gs-bg: #f5f7fb;

            --gs-card: #ffffff;

            --gs-text: #243447;

            --gs-muted: #7b8794;

            --gs-border: #e8edf3;

            --gs-success: #16a34a;
            --gs-warning: #f59e0b;
            --gs-danger: #dc3545;

        }


        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: var(--gs-bg);

            color: var(--gs-text);

            font-family:
                Arial,
                Helvetica,
                sans-serif;

        }


        a {
            text-decoration: none;
        }


        /* ==================================================
           SIDEBAR
        ================================================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 260px;
            height: 100vh;

            background:
                linear-gradient(
                    180deg,
                    var(--gs-dark),
                    var(--gs-sidebar)
                );

            color: #fff;

            z-index: 1050;

            overflow-y: auto;

            transition: .3s ease;

        }


        .sidebar-brand {

            height: 82px;

            padding: 20px;

            display: flex;

            align-items: center;

            gap: 12px;

            border-bottom:
                1px solid
                rgba(255,255,255,.08);

        }


        .brand-logo {

            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    var(--gs-primary),
                    var(--gs-secondary)
                );

            font-size: 19px;

        }


        .brand-title {

            font-size: 15px;

            font-weight: 800;

            letter-spacing: .5px;

        }


        .brand-subtitle {

            margin-top: 4px;

            font-size: 10px;

            color: #8fa8bc;

            text-transform: uppercase;

            letter-spacing: 1px;

        }


        .sidebar-menu {

            padding: 22px 14px;

        }


        .menu-label {

            padding:
                0 12px 10px;

            font-size: 10px;

            color: #6f8ca3;

            font-weight: 700;

            letter-spacing: 1.3px;

            text-transform: uppercase;

        }


        .sidebar-link {

            display: flex;

            align-items: center;

            gap: 13px;

            padding:
                12px 13px;

            margin-bottom: 5px;

            border-radius: 10px;

            color: #aebfce;

            font-size: 13px;

            font-weight: 500;

            transition: .2s ease;

        }


        .sidebar-link i {

            width: 21px;

            font-size: 17px;

        }


        .sidebar-link:hover {

            background: #103955;

            color: #fff;

        }


        .sidebar-link.active {

            background:
                linear-gradient(
                    90deg,
                    rgba(13,110,253,.95),
                    rgba(0,184,217,.75)
                );

            color: #fff;

        }


        .sidebar-bottom {

            margin: 25px 14px;

            padding: 14px;

            border-radius: 12px;

            background:
                rgba(255,255,255,.05);

            border:
                1px solid
                rgba(255,255,255,.06);

        }


        .sidebar-admin {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .admin-avatar {

            width: 38px;
            height: 38px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    var(--gs-primary),
                    var(--gs-secondary)
                );

            color: #fff;

            font-size: 12px;

            font-weight: 700;

        }


        .admin-info {

            min-width: 0;

        }


        .admin-info strong {

            display: block;

            color: #fff;

            font-size: 12px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .admin-info span {

            display: block;

            color: #7891a4;

            font-size: 10px;

            margin-top: 2px;

        }


        /* ==================================================
           MAIN
        ================================================== */

        .main {

            margin-left: 260px;

            min-height: 100vh;

        }


        /* ==================================================
           TOPBAR
        ================================================== */

        .topbar {

            height: 82px;

            background: #fff;

            border-bottom:
                1px solid
                var(--gs-border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 30px;

            position: sticky;

            top: 0;

            z-index: 900;

        }


        .mobile-menu {

            display: none;

            border: none;

            background: transparent;

            font-size: 24px;

            color: var(--gs-dark);

        }


        .page-heading h1 {

            margin: 0;

            font-size: 21px;

            font-weight: 800;

            color: var(--gs-dark);

        }


        .page-heading p {

            margin: 4px 0 0;

            color: var(--gs-muted);

            font-size: 12px;

        }


        .topbar-right {

            display: flex;

            align-items: center;

            gap: 18px;

        }


        .top-admin {

            display: flex;

            align-items: center;

            gap: 9px;

            padding-left: 18px;

            border-left:
                1px solid
                var(--gs-border);

        }


        .top-admin-avatar {

            width: 38px;
            height: 38px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    var(--gs-primary),
                    var(--gs-secondary)
                );

            color: #fff;

            font-size: 12px;

            font-weight: 700;

        }


        .top-admin-info strong {

            display: block;

            font-size: 12px;

            color: var(--gs-dark);

        }


        .top-admin-info span {

            font-size: 10px;

            color: var(--gs-muted);

        }


        /* ==================================================
           CONTENT
        ================================================== */

        .content {

            padding: 30px;

        }


        /* ==================================================
           COMMON
        ================================================== */

        .panel {

            background: #fff;

            border:
                1px solid
                var(--gs-border);

            border-radius: 16px;

            overflow: hidden;

        }


        .panel-header {

            padding:
                17px 20px;

            border-bottom:
                1px solid
                var(--gs-border);

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .panel-header strong {

            font-size: 13px;

            color: var(--gs-dark);

        }


        .dashboard-footer {

            padding:
                25px 0 5px;

            text-align: center;

            color: #9aa5b1;

            font-size: 10px;

        }


        /* ==================================================
           MOBILE
        ================================================== */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(0,0,0,.45);

            z-index: 1040;

        }


        @media (max-width: 991px) {

            .sidebar {

                transform:
                    translateX(-100%);

            }


            .sidebar.show {

                transform:
                    translateX(0);

            }


            .sidebar-overlay.show {

                display: block;

            }


            .main {

                margin-left: 0;

            }


            .mobile-menu {

                display: block;

            }


            .topbar {

                padding:
                    0 20px;

            }


            .content {

                padding:
                    20px;

            }

        }


        @media (max-width: 575px) {

            .top-admin-info {

                display: none;

            }


            .page-heading h1 {

                font-size: 18px;

            }


            .page-heading p {

                display: none;

            }


            .content {

                padding:
                    15px;

            }

        }

    </style>

</head>

<body>