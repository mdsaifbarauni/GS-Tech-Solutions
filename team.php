<?php
// ==========================================================
// GS TECH SOLUTIONS - TEAM PAGE
// ==========================================================

require_once __DIR__ . '/config/database.php';

$page_title = "Our Team | GS Tech Solutions";

$team_members = [];

try {
    $stmt = $pdo->query(
        "SELECT *
         FROM team_members
         WHERE status = 1
         ORDER BY sort_order ASC, id ASC"
    );
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $team_members = [];
}

if (empty($team_members)) {
    $team_members = [
        [
            'name' => 'Gaurav',
            'designation' => 'Co-Founder & Software Developer',
            'phone' => '+916299716991',
            'email' => 'gstechsolutions2026@gmail.com',
            'profile_image' => 'assets/img/person/person-m-13.webp',
            'linkedin_url' => '#',
            'facebook_url' => '#',
            'instagram_url' => '#',
            'bio' => 'Focused on building practical software and business systems.'
        ],
        [
            'name' => 'Saif',
            'designation' => 'Co-Founder & Business Solutions',
            'phone' => '+919102075267',
            'email' => 'gstechsolutions2026@gmail.com',
            'profile_image' => 'assets/img/person/person-m-12.webp',
            'linkedin_url' => '#',
            'facebook_url' => '#',
            'instagram_url' => '#',
            'bio' => 'Helps businesses turn workflows and ideas into effective digital solutions.'
        ]
    ];
}

include 'includes/header.php';
?>

<main id="main" class="gs-main">

    <!-- ======================================================
         TEAM HERO
    ======================================================= -->

    <section class="gs-page-hero gs-about-page-hero">

        <div class="container position-relative">

            <div class="row align-items-center g-5">

                <div class="col-lg-7">

                    <div class="gs-hero-content">

                        <span class="gs-section-eyebrow">
                            <i class="bi bi-people"></i>
                            OUR TEAM
                        </span>

                        <h1>
                            The People Behind
                            <span>Practical Digital Growth.</span>
                        </h1>

                        <p>
                            GS Tech Solutions is built by people who care about
                            real business outcomes. We combine technical execution,
                            business understanding and clear communication to deliver
                            solutions that are useful, scalable and easy to manage.
                        </p>

                        <div class="gs-about-hero-actions">

                            <a href="#team-list" class="gs-btn gs-btn-primary">
                                Meet The Team
                                <i class="bi bi-arrow-down"></i>
                            </a>

                            <a href="contact.php" class="gs-btn gs-btn-outline">
                                Start A Conversation
                                <i class="bi bi-arrow-up-right"></i>
                            </a>

                        </div>

                        <div class="gs-hero-mini-stats">

                            <div>
                                <strong>2</strong>
                                <span>Founders</span>
                            </div>

                            <div>
                                <strong>100%</strong>
                                <span>Business Focus</span>
                            </div>

                            <div>
                                <strong>Hands-on</strong>
                                <span>Support</span>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-5">

                    <div class="gs-about-hero-visual">

                        <div class="gs-hero-glow"></div>

                        <div class="gs-about-main-card">

                            <div class="gs-about-card-header">

                                <div class="gs-window-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>

                                <span>
                                    team-overview.php
                                </span>

                            </div>

                            <div class="gs-about-code">

                                <div>
                                    <span class="code-tag">&lt;team&gt;</span>
                                </div>

                                <div class="indent">
                                    founders =
                                    <strong>Gaurav + Saif</strong>;
                                </div>

                                <div class="indent">
                                    focus =
                                    <strong>strategy + execution</strong>;
                                </div>

                                <div class="indent">
                                    support =
                                    <strong>business-first</strong>;
                                </div>

                                <div>
                                    <span class="code-tag">&lt;/team&gt;</span>
                                </div>

                            </div>

                            <div class="gs-code-status">

                                <span>
                                    <i class="bi bi-check-circle-fill"></i>
                                    Goal Driven
                                </span>

                                <span>
                                    <i class="bi bi-lightning-charge-fill"></i>
                                    Client Focused
                                </span>

                            </div>

                        </div>

                        <div class="gs-about-floating-card">

                            <div class="gs-floating-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>

                            <div>
                                <strong>Small Team. Big Impact.</strong>
                                <span>Focused on execution and trust</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ======================================================
         TEAM MEMBERS
    ======================================================= -->

    <section id="team-list" class="gs-section gs-section-light">

        <div class="container">

            <div class="gs-section-heading text-center">

                <span class="gs-section-eyebrow">
                    <i class="bi bi-person-badge"></i>
                    MEET OUR PEOPLE
                </span>

                <h2 class="gs-section-title">
                    A Team Built On
                    <span>Trust, Skill and Communication.</span>
                </h2>

                <p>
                    We believe the best technology solutions come from understanding both the business and the people who use them.
                </p>

            </div>

            <div class="row g-4 mt-4 justify-content-center">

                <?php foreach ($team_members as $index => $member): ?>
                    <?php
                        $name = $member['name'] ?? 'Team Member';
                        $designation = $member['designation'] ?? 'Team Member';
                        $phone = $member['phone'] ?? '';
                        $email = $member['email'] ?? '';
                        $profileImage = $member['profile_image'] ?? 'assets/img/person/person-m-13.webp';
                        $facebookUrl = $member['facebook_url'] ?? '#';
                        $instagramUrl = $member['instagram_url'] ?? '#';
                        $linkedinUrl = $member['linkedin_url'] ?? '#';
                    ?>

                    <div class="col-lg-5 col-md-6">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid" alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($designation, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="social">
                                    <?php if ($phone !== ''): ?>
                                        <a href="tel:<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" aria-label="Call <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-telephone"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($email !== ''): ?>
                                        <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" aria-label="Email <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-envelope"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($linkedinUrl !== '' && $linkedinUrl !== '#'): ?>
                                        <a href="<?= htmlspecialchars($linkedinUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                            <i class="bi bi-linkedin"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($facebookUrl !== '' && $facebookUrl !== '#'): ?>
                                        <a href="<?= htmlspecialchars($facebookUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($instagramUrl !== '' && $instagramUrl !== '#'): ?>
                                        <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                            <i class="bi bi-instagram"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h4>
                                <span><?= htmlspecialchars($designation, ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>

    </section>

    <!-- ======================================================
         WORKING APPROACH
    ======================================================= -->

    <section class="gs-section">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-6">

                    <span class="gs-section-eyebrow">
                        <i class="bi bi-lightning-charge"></i>
                        HOW WE WORK
                    </span>

                    <h2 class="gs-section-title">
                        Simple, clear and
                        <span>business-focused delivery.</span>
                    </h2>

                    <div class="gs-service-feature-list">
                        <span><i class="bi bi-check2"></i> Understand the actual business challenge</span>
                        <span><i class="bi bi-check2"></i> Recommend the most practical solution</span>
                        <span><i class="bi bi-check2"></i> Build and launch with clear communication</span>
                        <span><i class="bi bi-check2"></i> Support updates and improvements after launch</span>
                    </div>

                </div>

                <div class="col-lg-6">

                    <div class="gs-services-dashboard">

                        <div class="gs-dashboard-top">
                            <div class="gs-window-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <span>process-flow</span>
                            <i class="bi bi-three-dots"></i>
                        </div>

                        <div class="gs-dashboard-content">

                            <div class="gs-dashboard-heading">
                                <div>
                                    <small>TEAM APPROACH</small>
                                    <h3>From Idea <span>To Execution</span></h3>
                                </div>
                            </div>

                            <div class="gs-dashboard-grid">
                                <div>
                                    <i class="bi bi-chat-dots"></i>
                                    <span>Discuss</span>
                                    <strong>Goals</strong>
                                </div>
                                <div>
                                    <i class="bi bi-pen-nib"></i>
                                    <span>Design</span>
                                    <strong>Process</strong>
                                </div>
                                <div>
                                    <i class="bi bi-code-slash"></i>
                                    <span>Build</span>
                                    <strong>System</strong>
                                </div>
                                <div>
                                    <i class="bi bi-rocket-takeoff"></i>
                                    <span>Launch</span>
                                    <strong>Support</strong>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?php include 'includes/footer.php'; ?>
