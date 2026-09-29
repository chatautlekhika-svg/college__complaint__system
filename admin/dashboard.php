<?php

session_start();

/* ================================
   CHECK LOGIN
================================ */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


/* ================================
   CHECK ADMIN ROLE
================================ */

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Admin Dashboard - College Complaint Management System
    </title>

    <link rel="stylesheet"
          href="../assets/css/dashboard.css">

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

        <a href="complaints.php">
            📋 All Complaints
        </a>

        <a href="users.php">
            👥 Users
        </a>

        <a href="reports.php">
            📊 Reports
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


        <!-- ================================
             WELCOME PANEL
        ================================= -->

        <div class="welcome-panel">

            <div class="welcome-icon">
                🎓
            </div>

            <h1>
                Welcome,<br>
                <?php echo htmlspecialchars($_SESSION['name']); ?>!
            </h1>

            <p class="role">
                Administrator Dashboard
            </p>

            <div class="welcome-divider"></div>

            <p class="welcome-quote">
                "Better management builds a better college community."
            </p>

        </div>



        <!-- ================================
             DASHBOARD CARDS
        ================================= -->

        <div class="dashboard-cards">


            <!-- All Complaints -->

            <div class="dashboard-card complaints-card">

                <div class="card-icon">
                    📋
                </div>

                <h2>
                    All Complaints
                </h2>

                <p>
                    View and manage complaints submitted by students and teachers.
                </p>

                <a
                    href="complaints.php"
                    class="dashboard-button"
                >
                    View Complaints&nbsp; →
                </a>

            </div>



            <!-- Users -->

            <div class="dashboard-card profile-card-dashboard">

                <div class="card-icon">
                    👥
                </div>

                <h2>
                    Manage Users
                </h2>

                <p>
                    View registered students and teachers in the college system.
                </p>

                <a
                    href="users.php"
                    class="dashboard-button"
                >
                    View Users&nbsp; →
                </a>

            </div>



            <!-- Reports -->

            <div class="dashboard-card submit-card">

                <div class="card-icon">
                    📊
                </div>

                <h2>
                    Reports
                </h2>

                <p>
                    View complaint information and generate system reports.
                </p>

                <a
                    href="reports.php"
                    class="dashboard-button"
                >
                    View Reports&nbsp; →
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
                    View your administrator account information.
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
                    End your current administrator session securely.
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