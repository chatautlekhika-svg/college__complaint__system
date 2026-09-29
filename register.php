```php
<?php
include "config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // =========================
    // FORM VALIDATION
    // =========================

    if ($name == "") {
        $message = "Please enter your full name.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "Phone number must contain exactly 10 digits.";
        $message_type = "error";

    } elseif (!in_array($role, ['student', 'teacher'])) {
        $message = "Please select a valid role.";
        $message_type = "error";

    } elseif ($dob == "") {
        $message = "Please select your date of birth.";
        $message_type = "error";

    } elseif ($dob > date('Y-m-d')) {
        $message = "Date of birth cannot be a future date.";
        $message_type = "error";

    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $message = "Please select your gender.";
        $message_type = "error";

    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        // =========================
        // CHECK EMAIL
        // =========================

        $email_check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $email_check,
            "s",
            $email
        );

        mysqli_stmt_execute($email_check);
        mysqli_stmt_store_result($email_check);

        if (mysqli_stmt_num_rows($email_check) > 0) {

            $message = "Email already registered.";
            $message_type = "error";

        } else {

            // =========================
            // CHECK PHONE
            // =========================

            $phone_check = mysqli_prepare(
                $conn,
                "SELECT id FROM users WHERE phone = ?"
            );

            mysqli_stmt_bind_param(
                $phone_check,
                "s",
                $phone
            );

            mysqli_stmt_execute($phone_check);
            mysqli_stmt_store_result($phone_check);

            if (mysqli_stmt_num_rows($phone_check) > 0) {

                $message = "Phone number already registered.";
                $message_type = "error";

            } else {

                // =========================
                // HASH PASSWORD
                // =========================

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // =========================
                // INSERT USER
                // =========================

                $sql = mysqli_prepare(
                    $conn,
                    "INSERT INTO users
                    (name, email, password, phone, gender, dob, role)
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $sql,
                    "sssssss",
                    $name,
                    $email,
                    $hashed_password,
                    $phone,
                    $gender,
                    $dob,
                    $role
                );

                if (mysqli_stmt_execute($sql)) {

                    $message = "Registration successful!";
                    $message_type = "success";

                } else {

                    $message = "Registration failed. Please try again.";
                    $message_type = "error";
                }

                mysqli_stmt_close($sql);
            }

            mysqli_stmt_close($phone_check);
        }

        mysqli_stmt_close($email_check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - College Complaint Management System</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

    <style>

        /* =========================
           FORM VALIDATION STYLE
           ========================= */

        .form-group input:invalid:not(:placeholder-shown),
        .form-group select:invalid {
            border: 1px solid #dc3545;
        }

        .form-group input:valid,
        .form-group select:valid {
            border: 1px solid #28a745;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .form-group input:invalid:not(:placeholder-shown) + .error-message,
        .form-group select:invalid + .error-message {
            display: block;
        }

        .gender-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .password-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .field-error {
            border: 1px solid #dc3545 !important;
        }

        .field-valid {
            border: 1px solid #28a745 !important;
        }

    </style>

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


    <!-- Registration Section -->

    <section class="register-section">

        <div class="register-card">

            <img src="assets/images/mbmc-logo.png"
                 alt="MBMC Logo"
                 class="register-logo">

            <h1>Create Account</h1>

            <p class="register-subtitle">
                Register to use the College Complaint Management System
            </p>


            <!-- Success / Error Message -->

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


            <!-- Registration Form -->

            <form action="register.php"
                  method="POST"
                  id="registerForm">


                <!-- Full Name -->

                <div class="form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           placeholder="Enter your full name"
                           required>

                    <div class="error-message">
                        ⚠ Please enter your full name.
                    </div>

                </div>


                <!-- Email -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="Enter your email"
                           required>

                    <div class="error-message">
                        ⚠ Please enter a valid email address.
                    </div>

                </div>


                <!-- Phone -->

                <div class="form-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input type="tel"
                           id="phone"
                           name="phone"
                           placeholder="Enter 10 digit phone number"
                           maxlength="10"
                           pattern="[0-9]{10}"
                           inputmode="numeric"
                           required>

                    <div class="error-message">
                        ⚠ Phone number must contain exactly 10 digits.
                    </div>

                </div>


                <!-- Role -->

                <div class="form-group">

                    <label for="role">
                        Register As
                    </label>

                    <select id="role"
                            name="role"
                            required>

                        <option value="">
                            Select your role
                        </option>

                        <option value="student">
                            Student
                        </option>

                        <option value="teacher">
                            Teacher
                        </option>

                    </select>

                    <div class="error-message">
                        ⚠ Please select your role.
                    </div>

                </div>


                <!-- Date of Birth -->

                <div class="form-group">

                    <label for="dob">
                        Date of Birth
                    </label>

                    <input type="date"
                           id="dob"
                           name="dob"
                           required>

                    <div class="error-message">
                        ⚠ Please select your date of birth.
                    </div>

                </div>


                <!-- Gender -->

                <div class="form-group">

                    <label>
                        Gender
                    </label>

                    <div class="gender-options">

                        <label>
                            <input type="radio"
                                   name="gender"
                                   value="Male"
                                   required>
                            Male
                        </label>

                        <label>
                            <input type="radio"
                                   name="gender"
                                   value="Female">
                            Female
                        </label>

                        <label>
                            <input type="radio"
                                   name="gender"
                                   value="Other">
                            Other
                        </label>

                    </div>

                    <div class="gender-error">
                        ⚠ Please select your gender.
                    </div>

                </div>


                <!-- Password -->

                <div class="form-group">

                    <label for="register-password">
                        Password
                    </label>

                    <input type="password"
                           id="register-password"
                           name="password"
                           placeholder="Create a password"
                           minlength="8"
                           required>

                    <div class="error-message">
                        ⚠ Password must contain at least 8 characters.
                    </div>

                </div>


                <!-- Confirm Password -->

                <div class="form-group">

                    <label for="confirm-password">
                        Confirm Password
                    </label>

                    <input type="password"
                           id="confirm-password"
                           name="confirm_password"
                           placeholder="Confirm your password"
                           minlength="8"
                           required>

                    <div class="password-error">
                        ⚠ Passwords do not match.
                    </div>

                </div>


                <!-- Register Button -->

                <button type="submit"
                        class="register-submit">

                    CREATE ACCOUNT

                </button>

            </form>


            <!-- Login Link -->

            <p class="login-text">

                Already have an account?

                <a href="login.php">
                    Login
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


    <!-- =========================
         JAVASCRIPT VALIDATION
         ========================= -->

    <script>

        const form = document.getElementById("registerForm");

        const phone = document.getElementById("phone");

        const password = document.getElementById("register-password");

        const confirmPassword =
            document.getElementById("confirm-password");

        const passwordError =
            document.querySelector(".password-error");


        // =========================
        // PHONE NUMBER
        // =========================

        phone.addEventListener("input", function () {

            // Allow only numbers
            this.value = this.value.replace(/[^0-9]/g, "");

            if (this.value.length === 10) {

                this.classList.remove("field-error");
                this.classList.add("field-valid");

            } else {

                this.classList.remove("field-valid");
                this.classList.add("field-error");

            }

        });


        // =========================
        // PASSWORD MATCH
        // =========================

        function checkPassword() {

            if (confirmPassword.value === "") {

                passwordError.style.display = "none";
                confirmPassword.classList.remove("field-error");

                return;

            }

            if (password.value !== confirmPassword.value) {

                passwordError.style.display = "block";

                confirmPassword.classList.add("field-error");
                confirmPassword.classList.remove("field-valid");

            } else {

                passwordError.style.display = "none";

                confirmPassword.classList.remove("field-error");
                confirmPassword.classList.add("field-valid");

            }
        }


        password.addEventListener("input", checkPassword);

        confirmPassword.addEventListener("input", checkPassword);


        // =========================
        // FORM SUBMIT
        // =========================

        form.addEventListener("submit", function (event) {

            checkPassword();

            if (!form.checkValidity()) {

                event.preventDefault();

                form.reportValidity();

                return;

            }

            if (password.value !== confirmPassword.value) {

                event.preventDefault();

                passwordError.style.display = "block";

                confirmPassword.classList.add("field-error");

            }

        });

    </script>


</body>

</html>
```
