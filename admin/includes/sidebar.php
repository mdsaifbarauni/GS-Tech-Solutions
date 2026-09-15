<?php
// ==========================================================
// GS TECH SOLUTIONS
// Admin Sidebar
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('getInitials')) {
    function getInitials($name)
    {
        $name = trim($name);

        if ($name === '') {
            return 'A';
        }

        $words = preg_split('/\s+/', $name);

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 1));
        }

        return strtoupper(
            substr($words[0], 0, 1) .
            substr($words[count($words) - 1], 0, 1)
        );
    }
}
?>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ==========================================================
     ADMIN SIDEBAR
========================================================== -->

<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">

        <a href="index.php" class="brand-link">

            <div class="brand-logo">
                <i class="bi bi-code-slash"></i>
            </div>

            <div class="brand-text">
                <strong>GS TECH</strong>
                <span>SOLUTIONS</span>
            </div>

        </a>

        <!-- Mobile Close -->
        <button
            type="button"
            class="sidebar-close"
            id="sidebarClose"
            aria-label="Close Sidebar"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>


    <!-- Admin Profile -->
    <div class="sidebar-profile">

        <div class="profile-avatar">
            <?= e(getInitials($admin_name)); ?>
        </div>

        <div class="profile-info">
            <strong><?= e($admin_name); ?></strong>
            <span>
                <i class="bi bi-circle-fill"></i>
                Super Admin
            </span>
        </div>

    </div>


    <!-- Navigation -->
    <nav class="sidebar-nav">

        <!-- Main -->
        <div class="nav-section-title">
            MAIN MENU
        </div>


        <!-- Dashboard -->
        <a
            href="index.php"
            class="sidebar-link <?= $current_page === 'index.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-grid-1x2-fill"></i>
            </span>

            <span class="nav-text">
                Dashboard
            </span>
        </a>


        <!-- Users -->
        <a
            href="users.php"
            class="sidebar-link <?= $current_page === 'users.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-people-fill"></i>
            </span>

            <span class="nav-text">
                Users
            </span>
        </a>

        <a
            href="create-user.php"
            class="sidebar-link sub-link <?= $current_page === 'create-user.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-person-plus-fill"></i>
            </span>

            <span class="nav-text">
                Create User
            </span>
        </a>


        <!-- Services -->
        <a
            href="services.php"
            class="sidebar-link <?= $current_page === 'services.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-briefcase-fill"></i>
            </span>

            <span class="nav-text">
                Services
            </span>
        </a>


        <!-- Team -->
        <a
            href="team.php"
            class="sidebar-link <?= $current_page === 'team.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-people-fill"></i>
            </span>

            <span class="nav-text">
                Team
            </span>
        </a>


        <!-- FAQ -->
        <a
            href="faq.php"
            class="sidebar-link <?= $current_page === 'faq.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-question-circle-fill"></i>
            </span>

            <span class="nav-text">
                FAQ
            </span>
        </a>


        <!-- Inquiries -->
        <a
            href="inquiries.php"
            class="sidebar-link <?= $current_page === 'inquiries.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-chat-left-text-fill"></i>
            </span>

            <span class="nav-text">
                Inquiries
            </span>
        </a>


        <!-- Management -->
        <div class="nav-section-title">
            MANAGEMENT
        </div>


        <!-- Settings -->
        <a
            href="settings.php"
            class="sidebar-link <?= $current_page === 'settings.php' ? 'active' : ''; ?>"
        >
            <span class="nav-icon">
                <i class="bi bi-gear-fill"></i>
            </span>

            <span class="nav-text">
                Settings
            </span>
        </a>


        <!-- Website -->
        <a
            href="../index.php"
            target="_blank"
            class="sidebar-link"
        >
            <span class="nav-icon">
                <i class="bi bi-globe2"></i>
            </span>

            <span class="nav-text">
                Visit Website
            </span>

            <span class="external-icon">
                <i class="bi bi-box-arrow-up-right"></i>
            </span>
        </a>


    </nav>


    <!-- Sidebar Bottom -->
    <div class="sidebar-bottom">

        <a
            href="logout.php"
            class="sidebar-link logout-link"
        >
            <span class="nav-icon">
                <i class="bi bi-box-arrow-right"></i>
            </span>

            <span class="nav-text">
                Logout
            </span>
        </a>

    </div>

</aside>


<style>

/* ==========================================================
   GS TECH SOLUTIONS
   ADMIN SIDEBAR
========================================================== */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 270px;
    height: 100vh;

    background:
        linear-gradient(
            180deg,
            #071a2b 0%,
            #0a2238 55%,
            #061522 100%
        );

    color: #ffffff;

    display: flex;
    flex-direction: column;

    z-index: 1050;

    box-shadow: 8px 0 30px rgba(0, 0, 0, 0.12);

    overflow-y: auto;
    overflow-x: hidden;
}


/* Scrollbar */

.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
}


/* ==========================================================
   BRAND
========================================================== */

.sidebar-brand {
    height: 82px;

    padding: 0 20px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border-bottom: 1px solid rgba(255,255,255,0.08);
}


.brand-link {
    display: flex;
    align-items: center;
    gap: 12px;

    color: #ffffff;
    text-decoration: none;
}


.brand-logo {
    width: 43px;
    height: 43px;

    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: linear-gradient(
        135deg,
        #0d6efd,
        #00b8d9
    );

    box-shadow:
        0 8px 20px rgba(13,110,253,0.30);

    font-size: 21px;
}


.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
}


.brand-text strong {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.5px;
}


.brand-text span {
    font-size: 10px;
    font-weight: 600;

    letter-spacing: 2px;

    color: rgba(255,255,255,0.55);

    margin-top: 5px;
}


/* Close button */

.sidebar-close {
    display: none;

    border: 0;
    background: transparent;

    color: rgba(255,255,255,0.7);

    font-size: 19px;

    cursor: pointer;
}


/* ==========================================================
   PROFILE
========================================================== */

.sidebar-profile {
    margin: 20px 16px;

    padding: 14px;

    display: flex;
    align-items: center;

    gap: 12px;

    border-radius: 14px;

    background: rgba(255,255,255,0.055);

    border: 1px solid rgba(255,255,255,0.06);
}


.profile-avatar {
    width: 43px;
    height: 43px;

    flex-shrink: 0;

    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: linear-gradient(
        135deg,
        #0d6efd,
        #00b8d9
    );

    color: #ffffff;

    font-size: 14px;
    font-weight: 800;
}


.profile-info {
    min-width: 0;

    display: flex;
    flex-direction: column;
}


.profile-info strong {
    font-size: 13px;
    font-weight: 700;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


.profile-info span {
    margin-top: 4px;

    font-size: 10px;

    color: rgba(255,255,255,0.55);
}


.profile-info span i {
    font-size: 7px;

    color: #28d17c;

    margin-right: 4px;
}


/* ==========================================================
   NAVIGATION
========================================================== */

.sidebar-nav {
    flex: 1;

    padding: 4px 13px 20px;
}


.nav-section-title {
    padding: 18px 12px 8px;

    color: rgba(255,255,255,0.35);

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.5px;
}


.sidebar-link {
    position: relative;

    min-height: 47px;

    margin: 4px 0;

    padding: 0 13px;

    display: flex;
    align-items: center;

    gap: 12px;

    border-radius: 11px;

    color: rgba(255,255,255,0.65);

    text-decoration: none;

    font-size: 13px;
    font-weight: 600;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}


.sidebar-link:hover {
    color: #ffffff;

    background: rgba(255,255,255,0.07);

    transform: translateX(2px);
}


.sidebar-link.active {
    color: #ffffff;

    background:
        linear-gradient(
            90deg,
            rgba(13,110,253,0.95),
            rgba(0,184,217,0.72)
        );

    box-shadow:
        0 7px 18px rgba(13,110,253,0.20);
}


.sidebar-link.active::before {
    content: "";

    position: absolute;

    left: -13px;

    top: 8px;

    width: 3px;

    height: 31px;

    border-radius: 0 5px 5px 0;

    background: #ffffff;
}


.nav-icon {
    width: 22px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 16px;
}


.nav-text {
    flex: 1;
}


.external-icon {
    font-size: 11px;

    opacity: 0.5;
}


/* ==========================================================
   BOTTOM
========================================================== */

.sidebar-bottom {
    padding: 13px;

    border-top: 1px solid rgba(255,255,255,0.08);
}


.logout-link {
    color: rgba(255,255,255,0.60);
}


.logout-link:hover {
    color: #ff6b6b;

    background: rgba(255,70,70,0.08);

    transform: none;
}


/* ==========================================================
   MOBILE OVERLAY
========================================================== */

.sidebar-overlay {
    display: none;

    position: fixed;

    inset: 0;

    background: rgba(0,0,0,0.50);

    z-index: 1040;
}


/* ==========================================================
   RESPONSIVE
========================================================== */

@media (max-width: 991.98px) {

    .sidebar {
        transform: translateX(-100%);

        transition:
            transform 0.3s ease;

        box-shadow: 15px 0 40px rgba(0,0,0,0.25);
    }


    .sidebar.show {
        transform: translateX(0);
    }


    .sidebar-overlay.show {
        display: block;
    }


    .sidebar-close {
        display: flex;

        align-items: center;
        justify-content: center;

        width: 35px;
        height: 35px;

        border-radius: 9px;
    }


    .sidebar-close:hover {
        background: rgba(255,255,255,0.08);

        color: #ffffff;
    }

}

</style>