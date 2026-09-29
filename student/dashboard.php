<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard - College Complaint Management System</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>


<!-- ================================
     HEADER
================================ -->

<header class="dashboard-header">


    <div class="dashboard-brand">

        <img
            src="../assets/images/mbmc-logo.png"
            alt="MBMC Logo"
            class="dashboard-logo"
        >

        <div class="dashboard-brand-text">

            <h2>
                MADAN BHANDARI MEMORIAL COLLEGE
            </h2>

            <p>
                College Complaint Management System
            </p>

        </div>

    </div>


    <nav class="dashboard-nav">

        <a href="dashboard.php" class="active">
            🏠 Dashboard
        </a>

        <a href="submit_complaint.php">
            📝 Submit Complaint
        </a>

        <a href="my_complaints.php">
            📋 My Complaints
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a href="../logout.php" class="header-logout">
            🚪 Logout
        </a>

    </nav>

</header>


<!-- ================================
     MAIN
================================ -->

<main class="dashboard-main">

    <div class="dashboard-wrapper">


        <!-- WELCOME -->

        <div class="welcome-panel">

            <div class="welcome-icon">
                🎓
            </div>

            <h1>
                Welcome,<br>
                <?php echo htmlspecialchars($_SESSION['name']); ?>!
            </h1>

            <p class="role">
                Student Dashboard
            </p>

            <div class="welcome-divider"></div>

            <p class="welcome-quote">
                "Better communication builds a better college community."
            </p>

        </div>


        <!-- CARDS -->

        <div class="dashboard-cards">


            <!-- Submit Complaint -->

            <div class="dashboard-card submit-card">

                <div class="card-icon">
                    📝
                </div>

                <h2>
                    Submit Complaint
                </h2>

                <p>
                    Submit a new complaint to the college administration.
                </p>

                <a
                    href="submit_complaint.php"
                    class="dashboard-button"
                >
                    Submit Complaint&nbsp; →
                </a>

            </div>


            <!-- My Complaints -->

            <div class="dashboard-card complaints-card">

                <div class="card-icon">
                    📋
                </div>

                <h2>
                    My Complaints
                </h2>

                <p>
                    View the complaints you have submitted and check their status.
                </p>

                <a
                    href="my_complaints.php"
                    class="dashboard-button"
                >
                    View Complaints&nbsp; →
                </a>

            </div>


            <!-- My Profile -->

            <div class="dashboard-card profile-card-dashboard">

                <div class="card-icon">
                    👤
                </div>

                <h2>
                    My Profile
                </h2>

                <p>
                    View your registered student information.
                </p>

                <a
                    href="profile.php"
                    class="dashboard-button"
                >
                    View Profile&nbsp; →
                </a>

            </div>


            <!-- Logout -->

            <div class="dashboard-card logout-card">

                <div class="card-icon">
                    🚪
                </div>

                <h2>
                    Logout
                </h2>

                <p>
                    End your current session securely.
                </p>

                <a
                    href="../logout.php"
                    class="dashboard-button"
                >
                    Logout&nbsp; →
                </a>

            </div>


        </div>

    </div>

</main>


<!-- ================================
     FOOTER
================================ -->

<footer class="dashboard-footer">

    <h3>
        Madan Bhandari Memorial College
    </h3>

    <p>
        College Complaint Management System
    </p>

    <p>
        © 2026 Madan Bhandari Memorial College. All Rights Reserved.
    </p>

</footer>


</body>

</html>