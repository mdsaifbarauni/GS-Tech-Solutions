<?php

$page_title = "Contact Us | GS Tech Solutions";

$contact_success = false;
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    require_once __DIR__ . '/config/database.php';

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $project_type = trim($_POST['project_type'] ?? 'Other Requirement');
    $budget = trim($_POST['budget'] ?? '');
    $timeline = trim($_POST['timeline'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name !== '' && $email !== '' && $message !== '') {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS contact_inquiries (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(150) NOT NULL,
                    phone VARCHAR(20) NOT NULL,
                    project_type VARCHAR(150) NOT NULL,
                    budget VARCHAR(100) NULL,
                    timeline VARCHAR(100) NULL,
                    message TEXT NOT NULL,
                    source VARCHAR(100) NULL DEFAULT 'website',
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    status ENUM('new', 'contacted', 'in_progress', 'completed', 'closed') NOT NULL DEFAULT 'new',
                    assigned_to INT UNSIGNED NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            $stmt = $pdo->prepare("
                INSERT INTO contact_inquiries (
                    name, email, phone, project_type, budget, timeline, message, source, ip_address, user_agent, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'website', ?, ?, 'new')
            ");

            $stmt->execute([
                $name,
                $email,
                $phone,
                $project_type,
                $budget,
                $timeline,
                $message,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            $contact_success = true;

            $to = 'gstechsolutions2026@gmail.com';
            $subject = 'New inquiry: ' . $project_type;
            $body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nProject Type: {$project_type}\nBudget: {$budget}\nTimeline: {$timeline}\n\nMessage:\n{$message}";
            $headers = [
                'From: GS Tech Solutions <noreply@localhost>',
                'Reply-To: ' . $email,
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8'
            ];
            @mail($to, $subject, $body, implode("\r\n", $headers));

        } catch (PDOException $e) {
            $contact_error = 'Unable to submit your inquiry right now. Please try again later.';
        }
    } else {
        $contact_error = 'Please fill out all required fields.';
    }
}

include 'includes/header.php';

?>

<main id="main" class="gs-main">

    <!-- =========================================================
         CONTACT HERO
    ========================================================== -->
    <section class="gs-page-hero gs-contact-page-hero">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-7">

                    <div class="gs-page-hero-content">

                        <span class="gs-eyebrow">
                            <i class="bi bi-chat-square-text"></i>
                            Let's Work Together
                        </span>

                        <h1>
                            Let's Build Something That
                            <span>Works For Your Business.</span>
                        </h1>

                        <p>
                            Have a website idea, business software requirement,
                            ERP project or a custom solution in mind?
                            Tell us what you need and we'll help turn your
                            requirement into a practical digital solution.
                        </p>

                        <div class="gs-contact-hero-actions">

                            <a href="#contact-form" class="gs-btn gs-btn-primary">
                                Start Your Project
                                <i class="bi bi-arrow-right"></i>
                            </a>

                            <a href="services.php" class="gs-btn gs-btn-outline">
                                Explore Services
                                <i class="bi bi-grid"></i>
                            </a>

                        </div>

                        <div class="gs-contact-trust">

                            <div>
                                <i class="bi bi-check-circle-fill"></i>
                                Custom Solutions
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill"></i>
                                Clear Communication
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill"></i>
                                Long-Term Support
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Hero Visual -->
                <div class="col-lg-5">

                    <div class="gs-contact-hero-visual">

                        <div class="gs-contact-orbit orbit-one"></div>
                        <div class="gs-contact-orbit orbit-two"></div>

                        <div class="gs-contact-hero-card">

                            <div class="gs-contact-card-icon">
                                <i class="bi bi-send-check"></i>
                            </div>

                            <span>Project Inquiry</span>

                            <strong>
                                Let's discuss<br>
                                your next idea.
                            </strong>

                            <div class="gs-contact-mini-row">

                                <div>
                                    <i class="bi bi-globe2"></i>
                                </div>

                                <div>
                                    <small>Digital Solutions</small>
                                    <b>Web • Software • ERP</b>
                                </div>

                            </div>

                            <div class="gs-contact-mini-row">

                                <div>
                                    <i class="bi bi-headset"></i>
                                </div>

                                <div>
                                    <small>Support</small>
                                    <b>Business Focused</b>
                                </div>

                            </div>

                        </div>

                        <div class="gs-contact-floating floating-top">
                            <i class="bi bi-code-slash"></i>
                            <span>Development</span>
                        </div>

                        <div class="gs-contact-floating floating-bottom">
                            <i class="bi bi-database-check"></i>
                            <span>Reliable Systems</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         CONTACT INFORMATION
    ========================================================== -->
    <section class="gs-section gs-contact-info-section">

        <div class="container">

            <div class="gs-section-heading text-center">

                <span class="gs-section-label">
                    <i class="bi bi-info-circle"></i>
                    Get In Touch
                </span>

                <h2>
                    Let's Start A
                    <span>Conversation.</span>
                </h2>

                <p>
                    Share your requirement with us. Whether it's a simple
                    business website or a complete management system,
                    we're ready to understand your needs.
                </p>

            </div>


            <div class="row g-4 mt-2">

                <!-- Location -->
                <div class="col-lg-3 col-md-6">

                    <div class="gs-contact-info-card">

                        <div class="gs-contact-info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <span>Service Area</span>

                        <h4>North Bihar</h4>

                        <p>
                            Forbesganj • Araria<br>
                            Purnia • Kishanganj<br>
                            Bihar & Remote Services
                        </p>

                    </div>

                </div>


                <!-- Phone -->
                <div class="col-lg-3 col-md-6">

                    <div class="gs-contact-info-card">

                        <div class="gs-contact-info-icon">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <span>Call Us</span>

                        <h4>Let's Talk</h4>

                        <p>
                            <a href="tel:+916299716991">
                                +91 62997 16991
                            </a>
                            <br>

                            <a href="tel:+919102075267">
                                +91 91020 75267
                            </a>
                        </p>

                    </div>

                </div>


                <!-- Email -->
                <div class="col-lg-3 col-md-6">

                    <div class="gs-contact-info-card">

                        <div class="gs-contact-info-icon">
                            <i class="bi bi-envelope"></i>
                        </div>

                        <span>Email Us</span>

                        <h4>Write To Us</h4>

                        <p>
                            <a href="mailto:gstechsolutions2026@gmail.com">
                                gstechsolutions2026@gmail.com
                            </a>

                            <br>

                            <a href="mailto:Gaurav.kmkhg123@gmail.com">
                                Gaurav.kmkhg123@gmail.com
                            </a>
                        </p>

                    </div>

                </div>


                <!-- Hours -->
                <div class="col-lg-3 col-md-6">

                    <div class="gs-contact-info-card">

                        <div class="gs-contact-info-icon">
                            <i class="bi bi-clock"></i>
                        </div>

                        <span>Availability</span>

                        <h4>Business Hours</h4>

                        <p>
                            Monday – Saturday<br>
                            <strong>10:00 AM – 7:00 PM</strong><br>
                            Online & Remote Support
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         MAIN CONTACT FORM
    ========================================================== -->
    <section id="contact-form" class="gs-section gs-section-light gs-contact-main-section">

        <div class="container">

            <div class="row g-5 align-items-start">

                <!-- Left Content -->
                <div class="col-lg-5">

                    <div class="gs-contact-form-intro">

                        <span class="gs-section-label">
                            <i class="bi bi-pencil-square"></i>
                            Project Inquiry
                        </span>

                        <h2>
                            Tell Us About
                            <span>Your Requirement.</span>
                        </h2>

                        <p>
                            The more details you share, the better we can
                            understand your requirement and suggest the
                            right technology approach.
                        </p>


                        <div class="gs-contact-benefits">

                            <div class="gs-contact-benefit">

                                <div class="gs-benefit-icon">
                                    <i class="bi bi-lightbulb"></i>
                                </div>

                                <div>
                                    <h5>Understand Your Idea</h5>
                                    <p>
                                        We first understand your business,
                                        workflow and actual requirements.
                                    </p>
                                </div>

                            </div>


                            <div class="gs-contact-benefit">

                                <div class="gs-benefit-icon">
                                    <i class="bi bi-diagram-3"></i>
                                </div>

                                <div>
                                    <h5>Plan The Solution</h5>
                                    <p>
                                        We identify the right features,
                                        technology and development approach.
                                    </p>
                                </div>

                            </div>


                            <div class="gs-contact-benefit">

                                <div class="gs-benefit-icon">
                                    <i class="bi bi-rocket-takeoff"></i>
                                </div>

                                <div>
                                    <h5>Build & Support</h5>
                                    <p>
                                        Development doesn't stop at launch.
                                        We can provide ongoing technical support.
                                    </p>
                                </div>

                            </div>

                        </div>


                        <!-- Quick Contact -->
                        <div class="gs-quick-contact">

                            <div class="gs-quick-contact-icon">
                                <i class="bi bi-telephone-forward"></i>
                            </div>

                            <div>
                                <small>Prefer a direct conversation?</small>

                                <a href="tel:+916299716991">
                                    +91 62997 16991
                                </a>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Form -->
                <div class="col-lg-7">

                    <div class="gs-contact-form-card">

                        <div class="gs-form-header">

                            <div>
                                <span>Project Details</span>
                                <h3>Send Us Your Requirement</h3>
                            </div>

                            <div class="gs-form-icon">
                                <i class="bi bi-send"></i>
                            </div>

                        </div>

                        <?php if ($contact_success): ?>
                            <div class="alert alert-success mt-3 mb-3" role="alert">
                                <strong>Thank you!</strong> Your inquiry has been submitted successfully. We will contact you soon.
                            </div>
                        <?php elseif ($contact_error !== ''): ?>
                            <div class="alert alert-danger mt-3 mb-3" role="alert">
                                <?= htmlspecialchars($contact_error, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>

                        <form action="contact.php"
                              method="post"
                              class="gs-contact-form">

                            <div class="row g-3">

                                <!-- Name -->
                                <div class="col-md-6">

                                    <label for="name">
                                        Your Name
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-person"></i>

                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control"
                                            placeholder="Enter your name"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Email -->
                                <div class="col-md-6">

                                    <label for="email">
                                        Email Address
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-envelope"></i>

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            class="form-control"
                                            placeholder="Enter your email"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Phone -->
                                <div class="col-md-6">

                                    <label for="phone">
                                        Phone Number
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-telephone"></i>

                                        <input
                                            type="tel"
                                            id="phone"
                                            name="phone"
                                            class="form-control"
                                            placeholder="Enter your phone number"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Project Type -->
                                <div class="col-md-6">

                                    <label for="project_type">
                                        Project Type
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-grid"></i>

                                        <select
                                            id="project_type"
                                            name="project_type"
                                            class="form-control"
                                            required
                                        >

                                            <option value="">
                                                Select project type
                                            </option>

                                            <option value="Website Development">
                                                Website Development
                                            </option>

                                            <option value="Custom Software">
                                                Custom Software
                                            </option>

                                            <option value="Business Management Software">
                                                Business Management Software
                                            </option>

                                            <option value="School / College ERP">
                                                School / College ERP
                                            </option>

                                            <option value="Billing & Inventory">
                                                Billing & Inventory
                                            </option>

                                            <option value="Pharmacy Software">
                                                Pharmacy Software
                                            </option>

                                            <option value="E-Commerce">
                                                E-Commerce
                                            </option>

                                            <option value="Service Management">
                                                Service Management
                                            </option>

                                            <option value="Other Requirement">
                                                Other Requirement
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <!-- Budget -->
                                <div class="col-md-6">

                                    <label for="budget">
                                        Approx. Budget
                                        <small>(Optional)</small>
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-currency-rupee"></i>

                                        <select
                                            id="budget"
                                            name="budget"
                                            class="form-control"
                                        >

                                            <option value="">
                                                Select budget range
                                            </option>

                                            <option value="Below ₹15,000">
                                                Below ₹15,000
                                            </option>

                                            <option value="₹15,000 - ₹30,000">
                                                ₹15,000 – ₹30,000
                                            </option>

                                            <option value="₹30,000 - ₹60,000">
                                                ₹30,000 – ₹60,000
                                            </option>

                                            <option value="₹60,000 - ₹1,00,000">
                                                ₹60,000 – ₹1,00,000
                                            </option>

                                            <option value="₹1,00,000+">
                                                ₹1,00,000+
                                            </option>

                                            <option value="Need Guidance">
                                                Need Guidance
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <!-- Timeline -->
                                <div class="col-md-6">

                                    <label for="timeline">
                                        Expected Timeline
                                        <small>(Optional)</small>
                                    </label>

                                    <div class="gs-input-wrap">

                                        <i class="bi bi-calendar3"></i>

                                        <select
                                            id="timeline"
                                            name="timeline"
                                            class="form-control"
                                        >

                                            <option value="">
                                                Select timeline
                                            </option>

                                            <option value="As soon as possible">
                                                As soon as possible
                                            </option>

                                            <option value="Within 2 weeks">
                                                Within 2 weeks
                                            </option>

                                            <option value="Within 1 month">
                                                Within 1 month
                                            </option>

                                            <option value="1 - 3 months">
                                                1 – 3 months
                                            </option>

                                            <option value="Flexible">
                                                Flexible
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <!-- Message -->
                                <div class="col-12">

                                    <label for="message">
                                        Tell Us About Your Requirement
                                    </label>

                                    <div class="gs-textarea-wrap">

                                        <i class="bi bi-chat-left-text"></i>

                                        <textarea
                                            id="message"
                                            name="message"
                                            class="form-control"
                                            rows="6"
                                            placeholder="Describe your business, required features, number of users, existing system or anything else we should know..."
                                            required
                                        ></textarea>

                                    </div>

                                </div>


                                <!-- Submit -->
                                <div class="col-12">

                                    <button
                                        type="submit"
                                        class="gs-form-submit"
                                    >

                                        <span>
                                            Send Project Inquiry
                                        </span>

                                        <i class="bi bi-arrow-right"></i>

                                    </button>

                                    <p class="gs-form-note">
                                        <i class="bi bi-shield-check"></i>
                                        Your information will only be used
                                        to understand and respond to your inquiry.
                                    </p>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         WHAT WE CAN BUILD
    ========================================================== -->
    <section class="gs-section gs-contact-services">

        <div class="container">

            <div class="gs-section-heading text-center">

                <span class="gs-section-label">
                    <i class="bi bi-boxes"></i>
                    What We Build
                </span>

                <h2>
                    From Idea To
                    <span>Working Product.</span>
                </h2>

                <p>
                    We create practical digital products for businesses,
                    institutions and growing organizations.
                </p>

            </div>


            <div class="row g-4 mt-3">

                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            01
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-window-stack"></i>
                        </div>

                        <h4>Websites</h4>

                        <p>
                            Professional business websites,
                            landing pages and responsive web platforms.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>


                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            02
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-cpu"></i>
                        </div>

                        <h4>Business Software</h4>

                        <p>
                            Custom software for billing, inventory,
                            operations, reports and business workflows.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>


                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            03
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>

                        <h4>ERP Systems</h4>

                        <p>
                            School, college and organization management
                            systems built around real workflows.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>


                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            04
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-cart3"></i>
                        </div>

                        <h4>E-Commerce</h4>

                        <p>
                            Online stores with products, orders,
                            customers, payments and administration.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>


                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            05
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-receipt"></i>
                        </div>

                        <h4>Billing & Inventory</h4>

                        <p>
                            Stock management, billing, purchase,
                            sales and reporting solutions.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>


                <div class="col-lg-4 col-md-6">

                    <a href="services.php" class="gs-contact-service-card">

                        <div class="gs-contact-service-number">
                            06
                        </div>

                        <div class="gs-contact-service-icon">
                            <i class="bi bi-headset"></i>
                        </div>

                        <h4>AMC & Support</h4>

                        <p>
                            Technical maintenance, improvements,
                            troubleshooting and ongoing support.
                        </p>

                        <span>
                            Explore Service
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         CONTACT PROCESS
    ========================================================== -->
    <section class="gs-section gs-section-light gs-contact-process">

        <div class="container">

            <div class="gs-section-heading text-center">

                <span class="gs-section-label">
                    <i class="bi bi-signpost-split"></i>
                    Simple Process
                </span>

                <h2>
                    What Happens
                    <span>After You Contact Us?</span>
                </h2>

            </div>


            <div class="gs-contact-process-grid">

                <div class="gs-contact-process-card">

                    <span>01</span>

                    <div class="gs-contact-process-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>

                    <h4>Discussion</h4>

                    <p>
                        We discuss your requirement, business workflow
                        and expected outcome.
                    </p>

                </div>


                <div class="gs-contact-process-card">

                    <span>02</span>

                    <div class="gs-contact-process-icon">
                        <i class="bi bi-puzzle"></i>
                    </div>

                    <h4>Solution Planning</h4>

                    <p>
                        We break your requirement into practical
                        features and modules.
                    </p>

                </div>


                <div class="gs-contact-process-card">

                    <span>03</span>

                    <div class="gs-contact-process-icon">
                        <i class="bi bi-code-square"></i>
                    </div>

                    <h4>Development</h4>

                    <p>
                        The solution is designed and developed using
                        suitable modern technologies.
                    </p>

                </div>


                <div class="gs-contact-process-card">

                    <span>04</span>

                    <div class="gs-contact-process-icon">
                        <i class="bi bi-rocket"></i>
                    </div>

                    <h4>Launch & Support</h4>

                    <p>
                        After testing and deployment, we can continue
                        supporting and improving the system.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         FINAL CTA
    ========================================================== -->
    <section class="gs-cta-section gs-contact-final-cta">

        <div class="container">

            <div class="gs-contact-final-box">

                <div class="gs-contact-final-content">

                    <span class="gs-section-label">
                        <i class="bi bi-stars"></i>
                        Have A Project In Mind?
                    </span>

                    <h2>
                        Your Requirement.
                        <span>Our Technology.</span>
                    </h2>

                    <p>
                        Let's discuss your idea and find the right
                        digital solution for your business.
                    </p>

                </div>

                <div class="gs-contact-final-actions">

                    <a href="#contact-form" class="gs-btn gs-btn-primary">
                        Start A Conversation
                        <i class="bi bi-arrow-right"></i>
                    </a>

                    <a href="tel:+916299716991" class="gs-btn gs-btn-light">
                        <i class="bi bi-telephone"></i>
                        Call Us
                    </a>

                </div>

            </div>

        </div>

    </section>

</main>

<?php include 'includes/footer.php'; ?>

