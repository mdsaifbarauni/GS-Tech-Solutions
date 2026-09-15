<?php

// ==========================================================
// GS TECH SOLUTIONS
// Admin - Company Settings
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

$page_title = "Company Settings | GS Tech Solutions";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS company_settings (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            company_name VARCHAR(150) NOT NULL,
            tagline VARCHAR(255) NULL,
            phone_primary VARCHAR(20) NULL,
            phone_secondary VARCHAR(20) NULL,
            email_primary VARCHAR(150) NULL,
            email_secondary VARCHAR(150) NULL,
            service_area VARCHAR(255) NULL,
            address VARCHAR(255) NULL,
            business_hours VARCHAR(255) NULL,
            logo VARCHAR(255) NULL,
            favicon VARCHAR(255) NULL,
            about_text TEXT NULL,
            mission TEXT NULL,
            vision TEXT NULL,
            facebook_url VARCHAR(255) NULL,
            instagram_url VARCHAR(255) NULL,
            linkedin_url VARCHAR(255) NULL,
            whatsapp_url VARCHAR(255) NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Unable to initialize company settings.";
}

// ==========================================================
// Load Settings
// ==========================================================

$settings = [
    'id' => 0,
    'company_name' => 'GS Tech Solutions',
    'tagline' => 'Web & Software Solutions',
    'phone_primary' => '6299716991',
    'phone_secondary' => '9102075267',
    'email_primary' => 'gstechsolutions2026@gmail.com',
    'email_secondary' => '',
    'service_area' => 'Forbesganj, Araria, Purnia, Kishanganj, Bihar',
    'address' => 'Bihar, India',
    'business_hours' => 'Mon - Sat: 9:00 AM - 7:00 PM',
    'logo' => '',
    'favicon' => '',
    'about_text' => '',
    'mission' => '',
    'vision' => '',
    'facebook_url' => '',
    'instagram_url' => '',
    'linkedin_url' => '',
    'whatsapp_url' => '',
    'status' => 1,
];

try {
    $stmt = $pdo->query("SELECT * FROM company_settings ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (is_array($row) && !empty($row)) {
        $settings = array_merge($settings, $row);
    }
} catch (PDOException $e) {
    $settings = $settings;
}

// ==========================================================
// Save Settings
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $phone_primary = trim($_POST['phone_primary'] ?? '');
    $phone_secondary = trim($_POST['phone_secondary'] ?? '');
    $email_primary = trim($_POST['email_primary'] ?? '');
    $email_secondary = trim($_POST['email_secondary'] ?? '');
    $service_area = trim($_POST['service_area'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $business_hours = trim($_POST['business_hours'] ?? '');
    $logo = trim($_POST['logo'] ?? '');
    $favicon = trim($_POST['favicon'] ?? '');
    $about_text = trim($_POST['about_text'] ?? '');
    $mission = trim($_POST['mission'] ?? '');
    $vision = trim($_POST['vision'] ?? '');
    $facebook_url = trim($_POST['facebook_url'] ?? '');
    $instagram_url = trim($_POST['instagram_url'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');
    $whatsapp_url = trim($_POST['whatsapp_url'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    if ($company_name === '') {
        $_SESSION['error_message'] = "Company name is required.";
        header("Location: settings.php");
        exit;
    }

    $payload = [
        $company_name,
        $tagline,
        $phone_primary,
        $phone_secondary,
        $email_primary,
        $email_secondary,
        $service_area,
        $address,
        $business_hours,
        $logo,
        $favicon,
        $about_text,
        $mission,
        $vision,
        $facebook_url,
        $instagram_url,
        $linkedin_url,
        $whatsapp_url,
        $status,
    ];

    try {
        if ((int) ($settings['id'] ?? 0) > 0) {
            $stmt = $pdo->prepare("
                UPDATE company_settings
                SET
                    company_name = ?,
                    tagline = ?,
                    phone_primary = ?,
                    phone_secondary = ?,
                    email_primary = ?,
                    email_secondary = ?,
                    service_area = ?,
                    address = ?,
                    business_hours = ?,
                    logo = ?,
                    favicon = ?,
                    about_text = ?,
                    mission = ?,
                    vision = ?,
                    facebook_url = ?,
                    instagram_url = ?,
                    linkedin_url = ?,
                    whatsapp_url = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([...$payload, (int) $settings['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO company_settings (
                    company_name,
                    tagline,
                    phone_primary,
                    phone_secondary,
                    email_primary,
                    email_secondary,
                    service_area,
                    address,
                    business_hours,
                    logo,
                    favicon,
                    about_text,
                    mission,
                    vision,
                    facebook_url,
                    instagram_url,
                    linkedin_url,
                    whatsapp_url,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute($payload);
        }

        $_SESSION['success_message'] = "Company settings saved successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Unable to save company settings.";
    }

    header("Location: settings.php");
    exit;
}

// ==========================================================
// Flash Messages
// ==========================================================

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
                <h1>Company Settings</h1>
                <p>Manage website branding, contact details, and social links</p>
            </div>

        </div>

        <div class="topbar-right">
            <div class="top-admin">
                <div class="top-admin-avatar">
                    <?= e(getInitials($_SESSION['admin_name'] ?? 'Administrator')) ?>
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

        <div class="admin-panel">

            <div class="panel-header">
                <div>
                    <h2>Website Information</h2>
                    <p>Update brand details used across the site.</p>
                </div>
            </div>

            <form method="POST" action="settings.php" class="gs-settings-form">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="<?= e($settings['tagline'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Primary Phone</label>
                        <input type="text" name="phone_primary" class="form-control" value="<?= e($settings['phone_primary'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Secondary Phone</label>
                        <input type="text" name="phone_secondary" class="form-control" value="<?= e($settings['phone_secondary'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Primary Email</label>
                        <input type="email" name="email_primary" class="form-control" value="<?= e($settings['email_primary'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Secondary Email</label>
                        <input type="email" name="email_secondary" class="form-control" value="<?= e($settings['email_secondary'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Service Area</label>
                        <input type="text" name="service_area" class="form-control" value="<?= e($settings['service_area'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business Hours</label>
                        <input type="text" name="business_hours" class="form-control" value="<?= e($settings['business_hours'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?= e($settings['address'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Logo URL</label>
                        <input type="text" name="logo" class="form-control" value="<?= e($settings['logo'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Favicon URL</label>
                        <input type="text" name="favicon" class="form-control" value="<?= e($settings['favicon'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">About Text</label>
                        <textarea name="about_text" class="form-control" rows="5"><?= e($settings['about_text'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mission</label>
                        <textarea name="mission" class="form-control" rows="4"><?= e($settings['mission'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vision</label>
                        <textarea name="vision" class="form-control" rows="4"><?= e($settings['vision'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="<?= e($settings['facebook_url'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="<?= e($settings['instagram_url'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control" value="<?= e($settings['linkedin_url'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">WhatsApp URL</label>
                        <input type="url" name="whatsapp_url" class="form-control" value="<?= e($settings['whatsapp_url'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="settingsStatus"
                                name="status"
                                <?= ((int) ($settings['status'] ?? 1) === 1) ? 'checked' : '' ?>
                            >
                            <label class="form-check-label" for="settingsStatus">Active</label>
                        </div>
                    </div>

                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Save Settings
                    </button>
                </div>

            </form>

        </div>

    </div>

</main>

<style>
    .content-body {
        padding: 30px 24px 40px;
    }

    .gs-admin-alert {
        margin-bottom: 20px;
    }

    .admin-panel {
        background: var(--gs-card, #fff);
        border: 1px solid var(--gs-border, #e8edf3);
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(8, 29, 48, 0.06);
        padding: 24px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .panel-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--gs-dark, #071a2b);
    }

    .panel-header p {
        margin: 6px 0 0;
        color: var(--gs-muted, #7b8794);
    }

    .gs-settings-form .form-label {
        font-weight: 600;
        color: var(--gs-text, #243447);
        margin-bottom: 8px;
    }

    .gs-settings-form .form-control,
    .gs-settings-form .form-select {
        border-radius: 12px;
        border: 1px solid var(--gs-border, #e8edf3);
        padding: 0.7rem 0.9rem;
        box-shadow: none;
    }

    .gs-settings-form .form-control:focus,
    .gs-settings-form .form-select:focus {
        border-color: rgba(13, 110, 253, 0.4);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    @media (max-width: 767px) {
        .content-body {
            padding: 20px 14px 30px;
        }

        .admin-panel {
            padding: 18px 14px;
        }
    }
</style>

<?php
// ==========================================================
// END OF PAGE
// ==========================================================
?>
