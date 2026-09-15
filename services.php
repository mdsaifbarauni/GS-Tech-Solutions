<?php
// ==========================================================
// GS TECH SOLUTIONS - SERVICES PAGE
// ==========================================================

require_once __DIR__ . '/config/database.php';

$page_title = "Services | GS Tech Solutions";

$services = [];

try {
    $stmt = $pdo->query(
        "SELECT *
         FROM services
         WHERE status = 1
         ORDER BY sort_order ASC, id ASC"
    );

    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
}

$service_defaults = [
    [
        'title' => 'Website Development',
        'slug' => 'website',
        'short_description' => 'Professional and responsive websites designed to build your brand and attract customers.',
        'description' => 'Professional website development for businesses, startups and organizations.',
        'icon' => 'bi bi-window-stack',
        'featured' => 0,
        'label' => 'WEB DEVELOPMENT'
    ],
    [
        'title' => 'Custom Software Development',
        'slug' => 'software',
        'short_description' => 'Business software built around your workflow, users and operational requirements.',
        'description' => 'Custom software systems developed to match your actual business process.',
        'icon' => 'bi bi-code-square',
        'featured' => 1,
        'label' => 'SOFTWARE DEVELOPMENT'
    ],
    [
        'title' => 'ERP Systems',
        'slug' => 'erp',
        'short_description' => 'Centralized management platforms for operations, users, data and reports.',
        'description' => 'ERP and management systems that bring your operations into one connected platform.',
        'icon' => 'bi bi-diagram-3',
        'featured' => 0,
        'label' => 'MANAGEMENT SYSTEMS'
    ],
    [
        'title' => 'School / College Management',
        'slug' => 'education',
        'short_description' => 'Complete education management solutions for institutions and coaching centers.',
        'description' => 'Academic and administrative tools for student management and institutional operations.',
        'icon' => 'bi bi-mortarboard',
        'featured' => 0,
        'label' => 'EDUCATION SOFTWARE'
    ],
    [
        'title' => 'Billing & Inventory Software',
        'slug' => 'billing',
        'short_description' => 'Manage stock, sales, purchases and invoices from a unified platform.',
        'description' => 'Business management tools for sales, purchase, inventory and billing.',
        'icon' => 'bi bi-receipt',
        'featured' => 0,
        'label' => 'BUSINESS MANAGEMENT'
    ],
    [
        'title' => 'Pharmacy Management',
        'slug' => 'pharmacy',
        'short_description' => 'Pharmacy operations, stock tracking and billing in a single system.',
        'description' => 'Healthcare software designed for pharmacy workflows and reporting.',
        'icon' => 'bi bi-capsule',
        'featured' => 0,
        'label' => 'HEALTHCARE SOFTWARE'
    ]
];

if (empty($services)) {
    $services = $service_defaults;
}

function gs_service_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

include 'includes/header.php';
?>

<!-- ==========================================================
     SERVICES PAGE HERO
=========================================================== -->

<section class="gs-page-hero gs-services-page-hero">

    <div class="container position-relative">

        <div class="row align-items-center g-5">

            <!-- HERO CONTENT -->
            <div class="col-lg-7">

                <span class="gs-section-eyebrow">
                    <i class="bi bi-grid-1x2"></i>
                    OUR SERVICES
                </span>

                <h1>
                    Digital Solutions
                    <span>Built Around Your Business.</span>
                </h1>

                <p>
                    From professional websites to complete business
                    management systems, we design and develop practical
                    technology solutions that help organizations work
                    smarter, faster and more efficiently.
                </p>

                <div class="gs-service-hero-actions">

                    <a href="#services-list"
                       class="gs-btn gs-btn-primary">

                        Explore Services

                        <i class="bi bi-arrow-down"></i>

                    </a>

                    <a href="contact.php"
                       class="gs-btn gs-btn-outline">

                        Discuss Your Requirement

                        <i class="bi bi-arrow-up-right"></i>

                    </a>

                </div>

                <!-- TRUST POINTS -->

                <div class="gs-service-trust-row">

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Custom Solutions</span>
                    </div>

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Responsive Design</span>
                    </div>

                    <div>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Long-Term Support</span>
                    </div>

                </div>

            </div>


            <!-- HERO VISUAL -->

            <div class="col-lg-5">

                <div class="gs-services-hero-visual">

                    <div class="gs-service-orb orb-one"></div>
                    <div class="gs-service-orb orb-two"></div>

                    <div class="gs-services-dashboard">

                        <div class="gs-dashboard-top">

                            <div class="gs-window-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>

                            <span>
                                gs-tech-solutions
                            </span>

                            <i class="bi bi-three-dots"></i>

                        </div>


                        <div class="gs-dashboard-content">

                            <div class="gs-dashboard-heading">

                                <div>

                                    <small>
                                        DIGITAL SOLUTIONS
                                    </small>

                                    <h3>
                                        Your Business
                                        <span>Dashboard</span>
                                    </h3>

                                </div>

                                <div class="gs-dashboard-status">

                                    <i class="bi bi-check-circle-fill"></i>

                                    Active

                                </div>

                            </div>


                            <div class="gs-dashboard-grid">

                                <div>

                                    <i class="bi bi-window-stack"></i>

                                    <span>
                                        Websites
                                    </span>

                                    <strong>
                                        Modern
                                    </strong>

                                </div>


                                <div>

                                    <i class="bi bi-code-square"></i>

                                    <span>
                                        Software
                                    </span>

                                    <strong>
                                        Custom
                                    </strong>

                                </div>


                                <div>

                                    <i class="bi bi-diagram-3"></i>

                                    <span>
                                        ERP
                                    </span>

                                    <strong>
                                        Scalable
                                    </strong>

                                </div>


                                <div>

                                    <i class="bi bi-headset"></i>

                                    <span>
                                        Support
                                    </span>

                                    <strong>
                                        Reliable
                                    </strong>

                                </div>

                            </div>


                            <div class="gs-dashboard-progress">

                                <div class="progress-label">

                                    <span>
                                        Project Progress
                                    </span>

                                    <strong>
                                        92%
                                    </strong>

                                </div>

                                <div class="progress">

                                    <div
                                        class="progress-bar"
                                        style="width:92%">
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- FLOATING CARD -->

                    <div class="gs-service-floating-card">

                        <div class="gs-floating-icon">

                            <i class="bi bi-lightning-charge-fill"></i>

                        </div>

                        <div>

                            <strong>
                                Smart Solutions
                            </strong>

                            <span>
                                Built for real business needs
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- ==========================================================
     SERVICES INTRO
=========================================================== -->

<section class="gs-section gs-services-intro">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-5">

                <span class="gs-section-eyebrow">

                    <i class="bi bi-stars"></i>

                    WHAT WE OFFER

                </span>

                <h2 class="gs-section-title">

                    Technology That

                    <span>
                        Solves Real Problems.
                    </span>

                </h2>

            </div>


            <div class="col-lg-7">

                <p class="gs-section-text mb-0">

                    Every organization has different requirements.
                    That's why our services are designed around
                    your actual workflow instead of forcing your
                    business to adapt to generic software.

                </p>

            </div>

        </div>

    </div>

</section>



<!-- ==========================================================
     SERVICES LIST
=========================================================== -->

<section id="services-list"
         class="gs-section gs-section-light gs-services-list-section">

    <div class="container">


        <!-- SECTION HEADING -->

        <div class="gs-section-heading text-center">

            <span class="gs-section-eyebrow">

                <i class="bi bi-stack"></i>

                OUR CORE SERVICES

            </span>

            <h2 class="gs-section-title">

                Complete Digital Solutions

                <span>
                    For Growing Organizations.
                </span>

            </h2>

            <p>

                Choose the service you need or contact us
                for a completely customized solution.

            </p>

        </div>



        <div class="row g-4 mt-4">
            <?php foreach ($services as $index => $service): ?>
                <?php
                    $title = $service['service_name'] ?? $service['title'] ?? 'Service';
                    $slug = $service['slug'] ?? strtolower(str_replace(' ', '-', $title));
                    $icon = $service['icon'] ?? 'bi bi-grid';
                    $description = $service['short_description'] ?? ($service['description'] ?? '');
                    $label = strtoupper($service['service_number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT));
                    $featured = ($index % 3 === 1);
                    $number = $service['service_number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                ?>

                <div class="col-lg-6">
                    <div class="gs-service-detail-card <?= $featured ? 'gs-service-featured-card' : '' ?>">
                        <div class="gs-service-detail-top">
                            <span class="gs-service-number"><?= gs_service_escape($number) ?></span>
                            <div class="gs-service-detail-icon">
                                <i class="<?= gs_service_escape($icon) ?>"></i>
                            </div>
                        </div>

                        <span class="gs-service-label"><?= gs_service_escape($label) ?></span>
                        <h3><?= gs_service_escape($title) ?></h3>
                        <p><?= gs_service_escape($description) ?></p>

                        <div class="gs-service-feature-list">
                            <span><i class="bi bi-check2"></i> Custom Solution</span>
                            <span><i class="bi bi-check2"></i> Business-Focused Workflow</span>
                            <span><i class="bi bi-check2"></i> Scalable Delivery</span>
                            <span><i class="bi bi-check2"></i> Long-Term Support</span>
                        </div>

                        <a href="contact.php?service=<?= gs_service_escape($slug) ?>" class="gs-service-detail-link">
                            View Service Details
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</section>



<!-- ==========================================================
     BUSINESS SOLUTION STRIP
=========================================================== -->

<section class="gs-service-business-strip">

    <div class="container">

        <div class="gs-business-strip-inner">

            <div class="gs-business-strip-icon">

                <i class="bi bi-buildings"></i>

            </div>


            <div>

                <span>
                    NOT SURE WHICH SERVICE YOU NEED?
                </span>

                <h3>
                    Tell us about your business.
                    <strong>We'll suggest the right solution.</strong>
                </h3>

            </div>


            <a href="contact.php"
               class="gs-btn gs-btn-light">

                Talk To Us

                <i class="bi bi-arrow-up-right"></i>

            </a>

        </div>

    </div>

</section>



<!-- ==========================================================
     PROCESS / WORKFLOW
=========================================================== -->

<section id="process"
         class="gs-section gs-service-process-section">

    <div class="container">


        <div class="gs-section-heading text-center">

            <span class="gs-section-eyebrow">

                <i class="bi bi-diagram-3"></i>

                OUR PROCESS

            </span>

            <h2 class="gs-section-title">

                From Requirement

                <span>
                    To Working Solution.
                </span>

            </h2>

            <p>

                A simple and transparent process designed
                to keep your project organized from start to finish.

            </p>

        </div>



        <div class="gs-services-process-grid">


            <!-- STEP 01 -->

            <div class="gs-services-process-card">

                <div class="gs-process-top">

                    <span>
                        01
                    </span>

                    <i class="bi bi-chat-square-text"></i>

                </div>

                <h3>
                    Understand
                </h3>

                <p>

                    We discuss your business, requirements,
                    workflow and project objectives.

                </p>

            </div>



            <!-- STEP 02 -->

            <div class="gs-services-process-card">

                <div class="gs-process-top">

                    <span>
                        02
                    </span>

                    <i class="bi bi-layout-text-window"></i>

                </div>

                <h3>
                    Plan & Design
                </h3>

                <p>

                    We plan the system structure, features,
                    interface and overall user experience.

                </p>

            </div>



            <!-- STEP 03 -->

            <div class="gs-services-process-card">

                <div class="gs-process-top">

                    <span>
                        03
                    </span>

                    <i class="bi bi-code-slash"></i>

                </div>

                <h3>
                    Develop
                </h3>

                <p>

                    Development, database integration,
                    testing and refinement happen step by step.

                </p>

            </div>



            <!-- STEP 04 -->

            <div class="gs-services-process-card">

                <div class="gs-process-top">

                    <span>
                        04
                    </span>

                    <i class="bi bi-rocket-takeoff"></i>

                </div>

                <h3>
                    Launch
                </h3>

                <p>

                    We deploy the project and help you
                    get started with the new system.

                </p>

            </div>



            <!-- STEP 05 -->

            <div class="gs-services-process-card">

                <div class="gs-process-top">

                    <span>
                        05
                    </span>

                    <i class="bi bi-headset"></i>

                </div>

                <h3>
                    Support
                </h3>

                <p>

                    Ongoing maintenance, updates and
                    technical assistance when required.

                </p>

            </div>

        </div>

    </div>

</section>



<!-- ==========================================================
     TECHNOLOGY
=========================================================== -->

<section class="gs-section gs-tech gs-services-tech">

    <div class="container">

        <div class="row align-items-center g-5">


            <div class="col-lg-6">

                <span class="gs-section-eyebrow">

                    <i class="bi bi-cpu"></i>

                    TECHNOLOGY

                </span>


                <h2 class="gs-section-title">

                    Modern Technology.

                    <span>
                        Practical Implementation.
                    </span>

                </h2>


                <p class="gs-section-text">

                    We use reliable and modern technologies
                    to build responsive websites, business
                    applications and database-driven systems.

                </p>


                <div class="gs-services-tech-points">

                    <div>

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Responsive & Mobile Friendly
                        </span>

                    </div>


                    <div>

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Database Driven Applications
                        </span>

                    </div>


                    <div>

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Scalable Architecture
                        </span>

                    </div>


                    <div>

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Maintainable Code
                        </span>

                    </div>

                </div>

            </div>



            <div class="col-lg-6">

                <div class="gs-services-tech-box">


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-filetype-php"></i>

                        </div>

                        <span>
                            PHP
                        </span>

                    </div>


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-box"></i>

                        </div>

                        <span>
                            Laravel
                        </span>

                    </div>


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-database"></i>

                        </div>

                        <span>
                            MySQL
                        </span>

                    </div>


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-bootstrap"></i>

                        </div>

                        <span>
                            Bootstrap
                        </span>

                    </div>


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-code-slash"></i>

                        </div>

                        <span>
                            JavaScript
                        </span>

                    </div>


                    <div class="gs-services-tech-item">

                        <div class="gs-tech-icon">

                            <i class="bi bi-lightning-charge"></i>

                        </div>

                        <span>
                            AJAX
                        </span>

                    </div>


                </div>

            </div>

        </div>

    </div>

</section>



<!-- ==========================================================
     FINAL CTA
=========================================================== -->

<section class="gs-cta-section gs-services-final-cta">

    <div class="container">

        <div class="gs-cta-box">


            <div>

                <span class="gs-section-eyebrow">

                    READY TO BUILD?

                </span>


                <h2>

                    Have A Business Idea?

                    <span>
                        Let's Turn It Into Reality.
                    </span>

                </h2>


                <p>

                    Whether you need a website, custom software,
                    ERP or complete business management system,
                    we're ready to discuss your requirements.

                </p>

            </div>


            <div class="gs-cta-actions">

                <a href="contact.php"
                   class="gs-btn gs-btn-light">

                    Start Your Project

                    <i class="bi bi-arrow-up-right"></i>

                </a>

            </div>

        </div>

    </div>

</section>


<?php include 'includes/footer.php'; ?>