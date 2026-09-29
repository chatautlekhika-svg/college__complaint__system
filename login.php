
<?php
session_start();
include "config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // =========================
    // FORM VALIDATION
    // =========================

    if ($email == "") {

        $message = "Please enter your email address.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif ($password == "") {

        $message = "Please enter your password.";
        $message_type = "error";

    } else {

        // =========================
        // FIND USER
        // =========================

        $sql = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $sql,
            "s",
            $email
        );

        mysqli_stmt_execute($sql);

        $result = mysqli_stmt_get_result($sql);

        // =========================
        // CHECK USER
        // =========================

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // =========================
            // CHECK PASSWORD
            // =========================

            if (password_verify($password, $user['password'])) {

                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // =========================
                // REDIRECT
                // =========================

               if ($user['role'] == 'student') {

    header("Location: student/dashboard.php");
    exit();

} elseif ($user['role'] == 'teacher') {

    header("Location: teacher/dashboard.php");
    exit();

} elseif ($user['role'] == 'admin') {

    header("Location: admin/dashboard.php");
    exit();

} else {

    $message = "Invalid user role.";
    $message_type = "error";
}

            } else {

                $message = "Incorrect email or password.";
                $message_type = "error";
            }

        } else {

            $message = "Incorrect email or password.";
            $message_type = "error";
        }

        mysqli_stmt_close($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - College Complaint Management System</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>


    <!-- Header -->

    <header class="header">

        <div class="logo-section">

            <img src="assets/images/mbmc-logo.png"
                 alt="MBMC Logo"
                 class="college-logo">

            <div>

                <h2>Madan Bhandari Memorial College</h2>

                <p>College Complaint Management System</p>

            </div>

        </div>


        <nav class="navbar">

            <a href="index.php">Home</a>

            <a href="#">About</a>

            <a href="#">Help</a>

            <a href="login.php"
               class="login-btn">
                Login
            </a>

        </nav>

    </header>


    <!-- Login Section -->

    <section class="login-section">

        <div class="login-card">


            <!-- Logo -->

            <img src="assets/images/mbmc-logo.png"
                 alt="MBMC Logo"
                 class="login-logo">


            <h1>Welcome Back</h1>


            <p class="login-subtitle">

                Login to your College Complaint Management account

            </p>


            <!-- Login Message -->

            <?php if ($message != ""): ?>

                <div style="
                    padding: 12px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                    text-align: center;

                    <?php
                    if ($message_type == 'success') {
                        echo 'background-color: #d4edda; color: #155724;';
                    } else {
                        echo 'background-color: #f8d7da; color: #721c24;';
                    }
                    ?>
                ">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- Login Form -->

            <form action="login.php"
                  method="POST">


                <!-- Email -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <!-- Password -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <div class="password-box">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >


                        <button
                            type="button"
                            onclick="togglePassword()">

                            👁

                        </button>

                    </div>

                </div>


                <!-- Login Options -->

                <div class="login-options">

                    <label>

                        <input
                            type="checkbox"
                            name="remember">

                        Remember me

                    </label>


                    <a href="#">
                        Forgot Password?
                    </a>

                </div>


                <!-- Login Button -->

                <button
                    type="submit"
                    class="login-submit">

                    LOGIN

                </button>


            </form>


            <!-- Register Link -->

            <p class="register-text">

                Don't have an account?

                <a href="register.php">
                    Register
                </a>

            </p>


        </div>

    </section>


    <!-- Footer -->

    <footer>

        <h3>
            Madan Bhandari Memorial College
        </h3>

        <p>
            College Complaint Management System
        </p>

        <p class="copyright">

            © 2026 Madan Bhandari Memorial College.
            All Rights Reserved.

        </p>

    </footer>


    <!-- Password Script -->

    <script>

        function togglePassword() {

            const password =
                document.getElementById("password");

            if (password.type === "password") {

                password.type = "text";

            } else {

                password.type = "password";

            }

        }

    </script>


</body>

</html>
