<?php
// ==========================================================
// GS TECH SOLUTIONS - HEADER
// ==========================================================

$page_title = $page_title ?? 'GS Tech Solutions | Web & Software Solutions';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

    <meta
        name="description"
        content="GS Tech Solutions provides professional website development, custom software, ERP, billing, inventory, e-commerce and business management solutions."
    >

    <meta
        name="keywords"
        content="GS Tech Solutions, website development, software development, ERP, business software, web development, Forbesganj, Araria, Purnia, Kishanganj, Bihar"
    >

    <meta name="author" content="GS Tech Solutions">


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="icon"
        href="assets/img/favicon.png"
    >

    <link
        rel="apple-touch-icon"
        href="assets/img/apple-touch-icon.png"
    >


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Poppins:wght@500;600;700;800;900&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         BOOTSTRAP 5
    ====================================================== -->

    <link
        href="assets/vendor/bootstrap/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         VENDOR CSS
    ====================================================== -->

    <link
        href="assets/vendor/aos/aos.css"
        rel="stylesheet"
    >

    <link
        href="assets/vendor/glightbox/css/glightbox.min.css"
        rel="stylesheet"
    >

    <link
        href="assets/vendor/swiper/swiper-bundle.min.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         ORIGINAL TEMPLATE CSS
    ====================================================== -->

    <link
        href="assets/css/main.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         GS TECH SOLUTIONS CUSTOM CSS
         MUST BE AFTER main.css
    ====================================================== -->

    <link
        href="assets/css/gs-tech.css"
        rel="stylesheet"
    >

</head>


<body class="gs-page">


<!-- =========================================================
     HEADER
========================================================= -->

<header
    id="header"
    class="gs-header fixed-top"
>

    <div class="container">

        <div class="gs-navbar">


            <!-- =================================================
                 LOGO
            ================================================== -->

            <a
                href="index.php"
                class="gs-logo"
                aria-label="GS Tech Solutions Home"
            >

                <div class="gs-logo-mark">
                    <span>G</span>
                    <span>S</span>
                </div>

                <div class="gs-logo-text">

                    <strong>GS TECH</strong>

                    <small>SOLUTIONS</small>

                </div>

            </a>


            <!-- =================================================
                 DESKTOP NAVIGATION
            ================================================== -->

            <nav
                id="navmenu"
                class="gs-navmenu"
                aria-label="Main Navigation"
            >

                <ul>

                    <li>
                        <a
                            href="index.php#hero"
                            class="active"
                        >
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="about.php">
                            About
                        </a>
                    </li>

                    <li>
                        <a href="services.php">
                            <i class="bi bi-grid"></i>
                            <span>Services</span>
                        </a>
                    </li>

                    <li>
                        <a href="solutions.php">
                            <i class="bi bi-diagram-3"></i>
                            <span>Solutions</span>
                        </a>
                    </li>

                    <li>
                        <a href="projects.php">
                            Projects
                        </a>
                    </li>

                    <li>
                        <a href="team.php">
                            Team
                        </a>
                    </li>

                    <li>
                        <a href="faq.php">
                            FAQ
                        </a>
                    </li>

                    <li>
                        <a href="contact.php">
    <i class="bi bi-envelope"></i>
    <span>Contact</span>
</a>
                    </li>

                </ul>

            </nav>


            <!-- =================================================
                 HEADER CTA
            ================================================== -->

            <a
                href="contact.php"
                class="gs-header-btn"
            >

                <span>Get Started</span>

                <i class="bi bi-arrow-up-right"></i>

            </a>


            <!-- =================================================
                 MOBILE BUTTON
            ================================================== -->

            <button
                type="button"
                class="gs-mobile-toggle"
                aria-label="Open menu"
                aria-controls="gs-mobile-menu"
                aria-expanded="false"
            >

                <i class="bi bi-list"></i>

            </button>

        </div>

    </div>

</header>


<!-- =========================================================
     MOBILE MENU
========================================================= -->

<div
    id="gs-mobile-menu"
    class="gs-mobile-menu"
    aria-hidden="true"
>

    <div class="gs-mobile-menu-header">

        <!-- Mobile Logo -->

        <a
            href="index.php"
            class="gs-logo"
        >

            <div class="gs-logo-mark">

                <span>G</span>
                <span>S</span>

            </div>

            <div class="gs-logo-text">

                <strong>GS TECH</strong>

                <small>SOLUTIONS</small>

            </div>

        </a>


        <!-- Close -->

        <button
            type="button"
            class="gs-mobile-close"
            aria-label="Close menu"
        >

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    <!-- =====================================================
         MOBILE LINKS
    ====================================================== -->

    <div class="gs-mobile-links">

        <a href="index.php#hero">

            <i class="bi bi-house"></i>

            <span>Home</span>

        </a>


        <a href="index.php#about">

            <i class="bi bi-building"></i>

            <span>About</span>

        </a>


        <a href="index.php#services">

            <i class="bi bi-grid"></i>

            <span>Services</span>

        </a>


        <a href="index.php#solutions">

            <i class="bi bi-diagram-3"></i>

            <span>Solutions</span>

        </a>


        <a href="index.php#portfolio">

            <i class="bi bi-briefcase"></i>

            <span>Projects</span>

        </a>


        <a href="team.php">

            <i class="bi bi-people"></i>

            <span>Team</span>

        </a>


        <a href="faq.php">

            <i class="bi bi-question-circle"></i>

            <span>FAQ</span>

        </a>


        <a href="contact.php">

            <i class="bi bi-envelope"></i>

            <span>Contact</span>

        </a>

    </div>


    <!-- =====================================================
         MOBILE CTA
    ====================================================== -->

    <div class="gs-mobile-cta">

        <small>
            Have a project idea?
        </small>

        <a href="index.php#contact">

            Start a Conversation

            <i class="bi bi-arrow-right"></i>

        </a>

    </div>

</div>


<!-- =========================================================
     MOBILE OVERLAY
========================================================= -->

<div
    id="gs-mobile-overlay"
    class="gs-mobile-overlay"
></div>


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<main id="main" class="gs-main">