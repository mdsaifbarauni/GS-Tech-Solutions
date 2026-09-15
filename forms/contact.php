<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */

  require_once __DIR__ . '/../config/database.php';

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $project_type = trim($_POST['project_type'] ?? '');
  $budget = trim($_POST['budget'] ?? '');
  $timeline = trim($_POST['timeline'] ?? '');
  $message = trim($_POST['message'] ?? '');
  $source = 'website';
  $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
  $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

  if ($project_type === '') {
      $project_type = 'Other Requirement';
  }

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
                  name,
                  email,
                  phone,
                  project_type,
                  budget,
                  timeline,
                  message,
                  source,
                  ip_address,
                  user_agent,
                  status
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')
          ");

          $stmt->execute([
              $name,
              $email,
              $phone,
              $project_type,
              $budget,
              $timeline,
              $message,
              $source,
              $ip_address,
              $user_agent
          ]);
      } catch (PDOException $e) {
          // Ignore DB insert errors and continue with email handling.
      }
  }

  // Replace contact@example.com with your real receiving email address
  $receiving_email_address = 'contact@example.com';

  if (file_exists($php_email_form = __DIR__ . '/../assets/vendor/php-email-form/php-email-form.php')) {
    include $php_email_form;
  } else {
    $fallback_subject = $project_type !== '' ? $project_type : 'New Inquiry';
    $fallback_body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nProject Type: {$project_type}\nBudget: {$budget}\nTimeline: {$timeline}\n\nMessage:\n{$message}";
    $fallback_headers = [
      'From: ' . $name . ' <' . $email . '>',
      'Reply-To: ' . $email,
      'MIME-Version: 1.0',
      'Content-Type: text/plain; charset=UTF-8'
    ];

    @mail($receiving_email_address, $fallback_subject, $fallback_body, implode("\r\n", $fallback_headers));
    echo 'OK';
    exit;
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $name;
  $contact->from_email = $email;
  $contact->subject = $project_type !== '' ? $project_type : 'New Inquiry';

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  $contact->add_message( $name, 'From');
  $contact->add_message( $email, 'Email');
  if($phone !== '') {
    $contact->add_message( $phone, 'Phone');
  }
  $contact->add_message( $message, 'Message', 10);

  echo $contact->send();
?>
