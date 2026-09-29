<?php

session_start();

include "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - College Complaint Management System</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <!-- Header -->

    <header class="header">

        <div class="logo-section">

            <img src="../assets/images/mbmc-logo.png"
                 alt="MBMC Logo"
                 class="college-logo">

            <div>

                <h2>Madan Bhandari Memorial College</h2>

                <p>College Complaint Management System</p>

            </div>

        </div>


        <!-- Navigation -->

        <nav class="navbar">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="complaints.php">
                All Complaints
            </a>

            <a href="users.php">
                Users
            </a>

            <a href="reports.php">
                Reports
            </a>

            <a href="profile.php" class="active">
                My Profile
            </a>

            <a href="../logout.php" class="login-btn">
                Logout
            </a>

        </nav>

    </header>


    <!-- Profile Section -->

    <section class="profile-section">

        <div class="profile-card">


            <!-- Heading -->

            <div class="profile-heading">

                <h1>My Profile</h1>

                <p>View your account information</p>

            </div>


            <!-- Profile Icon -->

            <div class="profile-icon">
                👤
            </div>


            <!-- Admin Name -->

            <h2 class="profile-name">

                <?php echo htmlspecialchars($user['name']); ?>

            </h2>


            <!-- Role -->

            <p class="profile-role">

                Admin

            </p>


            <!-- Profile Information -->

            <div class="profile-info">


                <div class="profile-item">

                    <span class="profile-label">
                        Full Name
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['name']); ?>

                    </span>

                </div>


                <div class="profile-item">

                    <span class="profile-label">
                        Email
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['email']); ?>

                    </span>

                </div>


                <div class="profile-item">

                    <span class="profile-label">
                        Phone
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['phone']); ?>

                    </span>

                </div>


                <div class="profile-item">

                    <span class="profile-label">
                        Role
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['role']); ?>

                    </span>

                </div>


                <div class="profile-item">

                    <span class="profile-label">
                        Date of Birth
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['dob']); ?>

                    </span>

                </div>


                <div class="profile-item">

                    <span class="profile-label">
                        Gender
                    </span>

                    <span class="profile-value">

                        <?php echo htmlspecialchars($user['gender']); ?>

                    </span>

                </div>


            </div>


            <!-- Buttons -->

            <div class="profile-buttons">

                <a href="dashboard.php" class="back-btn">
                    ← Back to Dashboard
                </a>

                <a href="../logout.php" class="logout-btn">
                    Logout
                </a>

            </div>


        </div>

    </section>

</body>

</html>