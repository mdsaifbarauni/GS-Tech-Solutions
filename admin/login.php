<?php

// ==========================================================
// GS TECH SOLUTIONS
// ADMIN LOGIN
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================================
// ALREADY LOGGED IN
// ==========================================================

if (
    isset($_SESSION['admin_id']) &&
    ($_SESSION['admin_role'] ?? '') === 'super_admin'
) {
    header("Location: index.php");
    exit;
}


// ==========================================================
// DATABASE CONNECTION
// ==========================================================

require_once "../config/database.php";

$error = "";


// ==========================================================
// LOGIN PROCESS
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        try {

            $sql = "
                SELECT
                    id,
                    name,
                    email,
                    password,
                    role,
                    status
                FROM users
                WHERE email = :email
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":email" => $email
            ]);

            $user = $stmt->fetch();


            // ==================================================
            // VERIFY ADMIN
            // ==================================================

            if (
                $user &&
                (int) $user["status"] === 1 &&
                $user["role"] === "super_admin" &&
                password_verify($password, $user["password"])
            ) {

                // Security: prevent session fixation
                session_regenerate_id(true);


                // Admin Session
                $_SESSION["admin_id"] =
                    (int) $user["id"];

                $_SESSION["admin_name"] =
                    $user["name"];

                $_SESSION["admin_email"] =
                    $user["email"];

                $_SESSION["admin_role"] =
                    $user["role"];


                // Redirect Dashboard
                header("Location: index.php");
                exit;

            } else {

                $error =
                    "Invalid email or password.";

            }

        } catch (PDOException $e) {

            $error =
                "Login failed. Please try again.";

        }

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

    <title>Admin Login | GS Tech Solutions</title>


    <!-- Bootstrap CSS -->

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
            --gs-dark-light: #0b2942;

        }


        * {

            box-sizing: border-box;

        }


        body {

            min-height: 100vh;

            margin: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            font-family:
                Arial,
                sans-serif;

            background:

                radial-gradient(
                    circle at top right,
                    rgba(0, 184, 217, 0.18),
                    transparent 30%
                ),

                linear-gradient(
                    135deg,
                    var(--gs-dark),
                    var(--gs-dark-light)
                );

        }


        /* Background Decorations */

        .login-shape {

            position: fixed;

            border-radius: 50%;

            pointer-events: none;

            filter: blur(2px);

        }


        .shape-1 {

            width: 350px;
            height: 350px;

            top: -120px;
            right: -100px;

            background:
                rgba(13, 110, 253, 0.16);

        }


        .shape-2 {

            width: 280px;
            height: 280px;

            bottom: -120px;
            left: -80px;

            background:
                rgba(0, 184, 217, 0.12);

        }


        /* Login Container */

        .login-wrapper {

            width: 100%;

            max-width: 460px;

            padding: 20px;

            position: relative;

            z-index: 2;

        }


        .login-card {

            background:
                rgba(255, 255, 255, 0.98);

            border-radius: 24px;

            padding: 40px;

            box-shadow:

                0 30px 80px
                rgba(0, 0, 0, 0.28);

            border:

                1px solid
                rgba(255, 255, 255, 0.4);

        }


        /* Brand */

        .brand-icon {

            width: 72px;
            height: 72px;

            margin: 0 auto 20px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 22px;

            background:

                linear-gradient(
                    135deg,
                    var(--gs-primary),
                    var(--gs-secondary)
                );

            color: #ffffff;

            font-size: 30px;

            box-shadow:

                0 15px 35px
                rgba(13, 110, 253, 0.30);

        }


        .brand-title {

            color: var(--gs-dark);

            font-size: 22px;

            font-weight: 800;

            letter-spacing: 0.3px;

        }


        .brand-subtitle {

            color: #718096;

            font-size: 13px;

        }


        /* Form */

        .form-label {

            color: #253858;

            font-size: 13px;

            font-weight: 600;

        }


        .input-group {

            border-radius: 12px;

        }


        .input-group-text {

            width: 52px;

            justify-content: center;

            border-radius:

                12px 0 0 12px;

            background: #f6f8fb;

            border-color: #e4e9f0;

            color: var(--gs-primary);

        }


        .form-control {

            height: 54px;

            border-radius:

                0 12px 12px 0;

            border-color: #e4e9f0;

            font-size: 14px;

        }


        .form-control:focus {

            border-color:
                var(--gs-primary);

            box-shadow:

                0 0 0 0.2rem
                rgba(13, 110, 253, 0.12);

        }


        /* Button */

        .btn-login {

            height: 54px;

            border: none;

            border-radius: 12px;

            font-size: 15px;

            font-weight: 600;

            color: white;

            background:

                linear-gradient(
                    135deg,
                    var(--gs-primary),
                    var(--gs-secondary)
                );

            transition: 0.25s ease;

        }


        .btn-login:hover {

            transform:
                translateY(-2px);

            color: white;

            box-shadow:

                0 12px 25px
                rgba(13, 110, 253, 0.28);

        }


        /* Footer */

        .login-footer {

            margin-top: 25px;

            text-align: center;

            color: #7c8798;

            font-size: 12px;

        }


        @media (max-width: 576px) {

            .login-card {

                padding: 30px 22px;

                border-radius: 20px;

            }


            .brand-title {

                font-size: 19px;

            }

        }

    </style>

</head>


<body>


<div class="login-shape shape-1"></div>

<div class="login-shape shape-2"></div>


<div class="login-wrapper">


    <div class="login-card">


        <!-- Brand -->

        <div class="text-center mb-4">


            <div class="brand-icon">

                <i class="bi bi-code-slash"></i>

            </div>


            <div class="brand-title">

                GS TECH SOLUTIONS

            </div>


            <div class="brand-subtitle mt-1">

                Secure Administration Portal

            </div>


        </div>



        <!-- Error Message -->

        <?php if (!empty($error)): ?>

            <div
                class="
                    alert
                    alert-danger
                    border-0
                    d-flex
                    align-items-center
                    gap-2
                    mb-4
                "
            >

                <i
                    class="
                        bi bi-exclamation-circle-fill
                    "
                ></i>


                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- Login Form -->

        <form
            method="POST"
            autocomplete="off"
        >


            <!-- Email -->

            <div class="mb-3">


                <label
                    class="form-label"
                >

                    Email Address

                </label>


                <div class="input-group">


                    <span
                        class="input-group-text"
                    >

                        <i
                            class="
                                bi bi-envelope
                            "
                        ></i>

                    </span>


                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                        autofocus
                    >


                </div>


            </div>



            <!-- Password -->

            <div class="mb-4">


                <label
                    class="form-label"
                >

                    Password

                </label>


                <div class="input-group">


                    <span
                        class="input-group-text"
                    >

                        <i
                            class="
                                bi bi-lock
                            "
                        ></i>

                    </span>


                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >


                </div>


            </div>



            <!-- Login Button -->

            <button
                type="submit"
                class="
                    btn
                    btn-login
                    w-100
                "
            >

                <i
                    class="
                        bi bi-box-arrow-in-right
                        me-2
                    "
                ></i>

                Login to Dashboard

            </button>


        </form>



        <!-- Footer -->

        <div class="login-footer">

            © <?= date("Y") ?>

            GS Tech Solutions

            <br>

            Authorized Access Only

        </div>


    </div>


</div>


</body>

</html>

