<?php

session_start();

include "../config.php";


/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


/* =========================================================
   CHECK ADMIN ROLE
========================================================= */

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}


/* =========================================================
   COMPLAINT COUNTS
========================================================= */

/* Total */

$sql_total = "
    SELECT COUNT(*) AS total
    FROM complaints
";

$result_total = mysqli_query($conn, $sql_total);

$total = mysqli_fetch_assoc($result_total)['total'];


/* Pending */

$sql_pending = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'Pending'
";

$result_pending = mysqli_query($conn, $sql_pending);

$pending = mysqli_fetch_assoc($result_pending)['total'];


/* In Progress */

$sql_progress = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'In Progress'
";

$result_progress = mysqli_query($conn, $sql_progress);

$progress = mysqli_fetch_assoc($result_progress)['total'];


/* Resolved */

$sql_resolved = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'Resolved'
";

$result_resolved = mysqli_query($conn, $sql_resolved);

$resolved = mysqli_fetch_assoc($result_resolved)['total'];


/* =========================================================
   COMPLAINTS BY ROLE
========================================================= */

/* Student */

$sql_student = "
    SELECT COUNT(*) AS total
    FROM complaints
    INNER JOIN users
        ON complaints.user_id = users.id
    WHERE users.role = 'student'
";

$result_student = mysqli_query($conn, $sql_student);

$student_complaints =
    mysqli_fetch_assoc($result_student)['total'];


/* Teacher */

$sql_teacher = "
    SELECT COUNT(*) AS total
    FROM complaints
    INNER JOIN users
        ON complaints.user_id = users.id
    WHERE users.role = 'teacher'
";

$result_teacher = mysqli_query($conn, $sql_teacher);

$teacher_complaints =
    mysqli_fetch_assoc($result_teacher)['total'];


/* =========================================================
   TOTAL USERS
========================================================= */

$sql_users = "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role != 'admin'
";

$result_users = mysqli_query($conn, $sql_users);

$total_users =
    mysqli_fetch_assoc($result_users)['total'];


/* =========================================================
   CATEGORY DATA
========================================================= */

$sql_category = "
    SELECT
        category,
        COUNT(*) AS total
    FROM complaints
    GROUP BY category
    ORDER BY total DESC
";

$result_category = mysqli_query($conn, $sql_category);


$category_data = [];

while ($category = mysqli_fetch_assoc($result_category)) {

    $category_data[] = $category;

}


/* Highest category value */

$max_category_count = 0;

foreach ($category_data as $category) {

    if (
        (int)$category['total']
        > $max_category_count
    ) {

        $max_category_count =
            (int)$category['total'];

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

    <title>
        Reports - College Complaint Management System
    </title>


    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >


    <style>

        /* =====================================================
           FORCE REPORT PAGE TO FULL WIDTH
        ===================================================== */

        html,
        body {

            width: 100%;

            max-width: 100%;

            margin: 0;

            padding: 0;

            overflow-x: hidden;

        }


        /* =====================================================
           REPORT MAIN
        ===================================================== */

        .reports-main {

            width: 100%;

            max-width: 100%;

            padding: 35px;

            box-sizing: border-box;

        }


        .reports-page {

            width: 100%;

            max-width: 1450px;

            margin: 0 auto;

            box-sizing: border-box;

        }


        /* =====================================================
           WELCOME
        ===================================================== */

        .reports-welcome {

            width: 100%;

            background: #eaf3ff;

            border-radius: 12px;

            padding: 32px 30px;

            box-sizing: border-box;

        }


        .reports-welcome .welcome-icon {

            width: 70px;

            height: 70px;

            background: white;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 38px;

            margin-bottom: 24px;

        }


        .reports-welcome h1 {

            margin: 0;

            color: #174a7c;

            font-size: 30px;

            line-height: 1.25;

        }


        .reports-welcome .role {

            margin-top: 12px;

            color: #68839d;

            font-size: 18px;

            line-height: 1.5;

        }


        .reports-welcome .welcome-divider {

            width: 100%;

            height: 1px;

            background: #d2dfec;

            margin: 30px 0 25px;

        }


        .reports-welcome .welcome-quote {

            color: #31577d;

            font-size: 17px;

            line-height: 1.6;

            font-style: italic;

        }


        /* =====================================================
           SUMMARY CARDS
        ===================================================== */

        .report-summary-grid {

            width: 100%;

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 20px;

            margin-top: 30px;

        }


        .report-summary-card {

            width: 100%;

            min-width: 0;

            background: white;

            border: 1px solid #d9e5f1;

            border-radius: 12px;

            padding: 25px 18px;

            text-align: center;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,0.06);

            box-sizing: border-box;

        }


        .report-summary-icon {

            font-size: 36px;

            margin-bottom: 12px;

        }


        .report-summary-card h2 {

            margin: 0 0 12px;

            color: #194d7f;

            font-size: 18px;

            line-height: 1.4;

        }


        .report-number {

            margin: 0;

            font-size: 34px;

            font-weight: bold;

            color: #31577d;

        }


        /* =====================================================
           SECOND ROW - 4 CARDS
        ===================================================== */

        .report-detail-grid {

            width: 100%;

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 20px;

            margin-top: 30px;

            align-items: stretch;

        }


        .report-detail-card {

            width: 100%;

            min-width: 0;

            background: white;

            border: 1px solid #d9e5f1;

            border-radius: 12px;

            padding: 25px 20px;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,0.06);

            box-sizing: border-box;

        }


        .detail-icon {

            text-align: center;

            font-size: 36px;

            margin-bottom: 12px;

        }


        .report-detail-card h2 {

            margin: 0;

            text-align: center;

            color: #194d7f;

            font-size: 18px;

            line-height: 1.4;

        }


        .detail-number {

            margin: 15px 0 8px;

            text-align: center;

            font-size: 34px;

            font-weight: bold;

            color: #31577d;

        }


        .detail-description {

            margin: 10px 0 0;

            text-align: center;

            color: #6b7280;

            font-size: 13px;

            line-height: 1.6;

        }


        /* =====================================================
           CATEGORY LIST
        ===================================================== */

        .category-list {

            margin-top: 20px;

            max-height: 220px;

            overflow-y: auto;

        }


        .category-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 10px;

            padding: 9px 0;

            border-bottom: 1px solid #eeeeee;

        }


        .category-row:last-child {

            border-bottom: none;

        }


        .category-name {

            color: #444;

            font-size: 13px;

            line-height: 1.4;

            word-break: break-word;

        }


        .category-count {

            flex-shrink: 0;

            min-width: 28px;

            padding: 4px 7px;

            border-radius: 15px;

            background: #f1f1f1;

            color: #123f6d;

            font-weight: bold;

            font-size: 12px;

            text-align: center;

        }


        /* =====================================================
           CHART
        ===================================================== */

        .chart-container {

            margin-top: 20px;

            max-height: 220px;

            overflow-y: auto;

        }


        .chart-row {

            margin-bottom: 14px;

        }


        .chart-row:last-child {

            margin-bottom: 0;

        }


        .chart-label {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 8px;

            margin-bottom: 5px;

            font-size: 12px;

        }


        .chart-label span:first-child {

            color: #444;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        .chart-value {

            color: #123f6d;

            font-weight: bold;

            flex-shrink: 0;

        }


        .chart-background {

            width: 100%;

            height: 10px;

            background: #eeeeee;

            border-radius: 10px;

            overflow: hidden;

        }


        .chart-bar {

            height: 100%;

            background: #123f6d;

            border-radius: 10px;

        }


        /* =====================================================
           REGISTERED USERS
        ===================================================== */

        .registered-users-card {

            width: 100%;

            margin-top: 30px;

            background: white;

            border: 1px solid #d9e5f1;

            border-radius: 12px;

            padding: 30px;

            text-align: center;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,0.06);

            box-sizing: border-box;

        }


        .registered-users-icon {

            font-size: 38px;

            margin-bottom: 10px;

        }


        .registered-users-card h2 {

            margin: 0 0 10px;

            color: #194d7f;

            font-size: 21px;

        }


        .registered-number {

            margin: 0;

            color: #31577d;

            font-size: 34px;

            font-weight: bold;

        }


        .registered-users-card p {

            margin: 8px 0 0;

            color: #6b7280;

            font-size: 14px;

        }


        /* =====================================================
           BACK BUTTON
        ===================================================== */

        .reports-back {

            margin-top: 30px;

            text-align: center;

        }


        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 1100px) {

            .report-summary-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }


            .report-detail-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 600px) {

            .reports-main {

                width: 100%;

                padding: 20px 10px;

            }


            .reports-page {

                width: 100%;

                max-width: 100%;

                margin: 0;

            }


            .reports-welcome {

                width: 100%;

                padding: 25px 18px;

            }


            .reports-welcome h1 {

                font-size: 25px;

            }


            .reports-welcome .role {

                font-size: 15px;

            }


            .reports-welcome .welcome-quote {

                font-size: 14px;

            }


            .report-summary-grid {

                width: 100%;

                grid-template-columns: 1fr;

                gap: 15px;

                margin-top: 20px;

            }


            .report-detail-grid {

                width: 100%;

                grid-template-columns: 1fr;

                gap: 15px;

                margin-top: 20px;

            }


            .report-summary-card,

            .report-detail-card,

            .registered-users-card {

                width: 100%;

                max-width: 100%;

            }


            .report-summary-card {

                padding: 22px 18px;

            }


            .report-detail-card {

                padding: 22px 18px;

            }


            .registered-users-card {

                padding: 25px 18px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

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

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="complaints.php">
            📋 All Complaints
        </a>

        <a href="users.php">
            👥 Users
        </a>

        <a
            href="reports.php"
            class="active"
        >
            📊 Reports
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a
            href="../logout.php"
            class="header-logout"
        >
            🚪 Logout
        </a>

    </nav>

</header>



<!-- =====================================================
     MAIN
===================================================== -->

<main class="reports-main">

    <div class="reports-page">


        <!-- =================================================
             TITLE
        ================================================== -->

        <section class="reports-welcome">

            <div class="welcome-icon">
                📊
            </div>

            <h1>
                Complaint Reports
            </h1>

            <p class="role">
                Overview of complaints and registered users
            </p>

            <div class="welcome-divider"></div>

            <p class="welcome-quote">
                Monitor complaint activity and resolution progress.
            </p>

        </section>



        <!-- =================================================
             FIRST ROW
        ================================================== -->

        <section class="report-summary-grid">


            <div class="report-summary-card">

                <div class="report-summary-icon">
                    📋
                </div>

                <h2>
                    Total Complaints
                </h2>

                <p class="report-number">
                    <?php echo $total; ?>
                </p>

            </div>


            <div class="report-summary-card">

                <div class="report-summary-icon">
                    🟡
                </div>

                <h2>
                    Pending
                </h2>

                <p class="report-number">
                    <?php echo $pending; ?>
                </p>

            </div>


            <div class="report-summary-card">

                <div class="report-summary-icon">
                    🔵
                </div>

                <h2>
                    In Progress
                </h2>

                <p class="report-number">
                    <?php echo $progress; ?>
                </p>

            </div>


            <div class="report-summary-card">

                <div class="report-summary-icon">
                    🟢
                </div>

                <h2>
                    Resolved
                </h2>

                <p class="report-number">
                    <?php echo $resolved; ?>
                </p>

            </div>


        </section>



        <!-- =================================================
             SECOND ROW
        ================================================== -->

        <section class="report-detail-grid">


            <!-- STUDENT -->

            <div class="report-detail-card">

                <div class="detail-icon">
                    🎓
                </div>

                <h2>
                    Student Complaints
                </h2>

                <p class="detail-number">
                    <?php echo $student_complaints; ?>
                </p>

                <p class="detail-description">
                    Total complaints submitted by students.
                </p>

            </div>



            <!-- TEACHER -->

            <div class="report-detail-card">

                <div class="detail-icon">
                    👨‍🏫
                </div>

                <h2>
                    Teacher Complaints
                </h2>

                <p class="detail-number">
                    <?php echo $teacher_complaints; ?>
                </p>

                <p class="detail-description">
                    Total complaints submitted by teachers.
                </p>

            </div>



            <!-- CATEGORY -->

            <div class="report-detail-card">

                <div class="detail-icon">
                    📂
                </div>

                <h2>
                    Category-wise Complaints
                </h2>

                <p class="detail-description">
                    Complaints grouped by category.
                </p>


                <div class="category-list">

                    <?php if (!empty($category_data)): ?>

                        <?php foreach (
                            $category_data
                            as $category
                        ): ?>

                            <div class="category-row">

                                <span class="category-name">

                                    <?php
                                    echo htmlspecialchars(
                                        $category['category']
                                    );
                                    ?>

                                </span>

                                <span class="category-count">

                                    <?php
                                    echo $category['total'];
                                    ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div
                            style="
                                padding: 25px 5px;
                                text-align: center;
                                color: #666;
                                font-size: 13px;
                            "
                        >
                            No category data available.
                        </div>

                    <?php endif; ?>

                </div>

            </div>



            <!-- CHART -->

            <div class="report-detail-card">

                <div class="detail-icon">
                    📈
                </div>

                <h2>
                    Complaint Chart
                </h2>

                <p class="detail-description">
                    Category-wise complaint distribution.
                </p>


                <div class="chart-container">

                    <?php if (!empty($category_data)): ?>

                        <?php foreach (
                            $category_data
                            as $category
                        ): ?>


                            <?php

                            $percentage = 0;

                            if (
                                $max_category_count > 0
                            ) {

                                $percentage =
                                    (
                                        (int)$category['total']
                                        /
                                        $max_category_count
                                    ) * 100;

                            }

                            ?>


                            <div class="chart-row">

                                <div class="chart-label">

                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            $category['category']
                                        );
                                        ?>

                                    </span>

                                    <span class="chart-value">

                                        <?php
                                        echo $category['total'];
                                        ?>

                                    </span>

                                </div>


                                <div class="chart-background">

                                    <div
                                        class="chart-bar"
                                        style="
                                            width:
                                            <?php
                                            echo $percentage;
                                            ?>%;
                                        "
                                    ></div>

                                </div>

                            </div>


                        <?php endforeach; ?>

                    <?php else: ?>

                        <div
                            style="
                                padding: 30px 5px;
                                text-align: center;
                                color: #666;
                                font-size: 13px;
                            "
                        >
                            No complaint data available.
                        </div>

                    <?php endif; ?>

                </div>

            </div>


        </section>



        <!-- =================================================
             REGISTERED USERS
        ================================================== -->

        <section class="registered-users-card">

            <div class="registered-users-icon">
                👥
            </div>

            <h2>
                Registered Users
            </h2>

            <p class="registered-number">
                <?php echo $total_users; ?>
            </p>

            <p>
                Total registered students and teachers in the system.
            </p>

        </section>



        <!-- =================================================
             BACK BUTTON
        ================================================== -->

        <div class="reports-back">

            <a
                href="dashboard.php"
                class="dashboard-button"
            >
                ← Back to Dashboard
            </a>

        </div>


    </div>

</main>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="dashboard-footer">

    <h3>
        Madan Bhandari Memorial College
    </h3>

    <p>
        College Complaint Management System
    </p>

    <p>
        © 2026 Madan Bhandari Memorial College.
        All Rights Reserved.
    </p>

</footer>


</body>

</html>