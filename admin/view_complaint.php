<?php

session_start();

include "../config.php";


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


/* ================================
   GET COMPLAINT ID
================================ */

$complaint_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($complaint_id <= 0) {
    header("Location: complaints.php");
    exit();
}


/* ================================
   UPDATE STATUS
================================ */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = $_POST['status'] ?? '';


    $allowed_status = [
        "Pending",
        "In Progress",
        "Resolved"
    ];


    if (in_array($status, $allowed_status)) {

        $sql_update = "
            UPDATE complaints
            SET status = ?
            WHERE id = ?
        ";


        $stmt = $conn->prepare(
            $sql_update
        );


        $stmt->bind_param(
            "si",
            $status,
            $complaint_id
        );


        $stmt->execute();

        $stmt->close();


        header(
            "Location: view_complaint.php?id="
            . $complaint_id
        );

        exit();
    }
}


/* ================================
   GET COMPLAINT DETAILS
================================ */

$sql = "
    SELECT
        complaints.id,
        complaints.subject,
        complaints.category,
        complaints.description,
        complaints.status,
        complaints.created_at,
        users.name,
        users.email,
        users.role
    FROM complaints
    INNER JOIN users
        ON complaints.user_id = users.id
    WHERE complaints.id = ?
";


$stmt = $conn->prepare(
    $sql
);


$stmt->bind_param(
    "i",
    $complaint_id
);


$stmt->execute();


$result =
    $stmt->get_result();


$complaint =
    $result->fetch_assoc();


$stmt->close();


if (!$complaint) {

    header(
        "Location: complaints.php"
    );

    exit();

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
        Complaint Details - Admin
    </title>


    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >


    <style>

        .view-complaint-main {

            width: 100%;

            padding: 40px;

            box-sizing: border-box;

        }


        .view-complaint-container {

            width: 100%;

            max-width: 900px;

            margin: 0 auto;

        }


        .view-complaint-card {

            background: white;

            border-radius: 12px;

            padding: 35px;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,0.08);

        }


        .view-complaint-title {

            margin: 0 0 30px;

            color: #123f6d;

            font-size: 28px;

        }


        .complaint-info-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;

        }


        .complaint-info-item {

            padding: 16px;

            background: #f7f9fc;

            border-radius: 8px;

        }


        .complaint-info-label {

            display: block;

            margin-bottom: 6px;

            color: #666;

            font-size: 12px;

            font-weight: bold;

        }


        .complaint-info-value {

            color: #333;

            font-size: 15px;

            line-height: 1.5;

            word-break: break-word;

        }


        /* =================================================
           DESCRIPTION
        ================================================= */

        .complaint-description {

            margin-top: 22px;

            padding: 20px;

            background: #f7f9fc;

            border-radius: 8px;

        }


        .complaint-description h3 {

            margin: 0 0 10px;

            color: #123f6d;

            font-size: 17px;

        }


        .complaint-description p {

            margin: 0;

            color: #444;

            font-size: 15px;

            line-height: 1.7;

            white-space: pre-wrap;

        }


        /* =================================================
           STATUS UPDATE BELOW DESCRIPTION
        ================================================= */

        .status-update-section {

            margin-top: 25px;

            padding-top: 25px;

            border-top: 1px solid #e5e7eb;

        }


        .status-update-section h3 {

            margin: 0 0 15px;

            color: #123f6d;

            font-size: 18px;

        }


        .status-update-form {

            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

        }


        .status-update-select {

            min-width: 190px;

            padding: 11px 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            background: white;

            font-size: 14px;

        }


        .status-update-button {

            padding: 11px 20px;

            border: none;

            border-radius: 6px;

            background: #123f6d;

            color: white;

            font-size: 14px;

            font-weight: bold;

            cursor: pointer;

        }


        .status-update-button:hover {

            background: #0e3155;

        }


        .back-complaints {

            margin-top: 25px;

        }


        .back-complaints a {

            color: #123f6d;

            text-decoration: none;

            font-weight: bold;

        }


        .back-complaints a:hover {

            text-decoration: underline;

        }


        /* =================================================
           MOBILE
        ================================================= */

        @media (max-width: 650px) {

            .view-complaint-main {

                padding: 20px 12px;

            }


            .view-complaint-card {

                padding: 22px 18px;

            }


            .view-complaint-title {

                font-size: 24px;

            }


            .complaint-info-grid {

                grid-template-columns: 1fr;

            }


            .status-update-form {

                flex-direction: column;

                align-items: stretch;

            }


            .status-update-select,
            .status-update-button {

                width: 100%;

                box-sizing: border-box;

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

        <a
            href="complaints.php"
            class="active"
        >
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

<main class="view-complaint-main">

    <div class="view-complaint-container">

        <div class="view-complaint-card">


            <h1 class="view-complaint-title">
                Complaint Details
            </h1>



            <!-- =================================================
                 COMPLAINT INFORMATION
            ================================================== -->

            <div class="complaint-info-grid">


                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Subject
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            $complaint['subject']
                        );
                        ?>

                    </div>

                </div>



                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Submitter
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            $complaint['name']
                        );
                        ?>

                    </div>

                </div>



                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Category
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            $complaint['category']
                        );
                        ?>

                    </div>

                </div>



                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Status
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            $complaint['status']
                        );
                        ?>

                    </div>

                </div>



                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Email
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            $complaint['email']
                        );
                        ?>

                    </div>

                </div>



                <div class="complaint-info-item">

                    <span class="complaint-info-label">
                        Role
                    </span>

                    <div class="complaint-info-value">

                        <?php
                        echo htmlspecialchars(
                            ucfirst(
                                $complaint['role']
                            )
                        );
                        ?>

                    </div>

                </div>


            </div>



            <!-- =================================================
                 DESCRIPTION
            ================================================== -->

            <div class="complaint-description">

                <h3>
                    Complaint Description
                </h3>

                <p>

                    <?php
                    echo htmlspecialchars(
                        $complaint['description']
                    );
                    ?>

                </p>

            </div>



            <!-- =================================================
                 STATUS UPDATE
            ================================================== -->

            <div class="status-update-section">

                <h3>
                    Update Complaint Status
                </h3>


                <form
                    method="POST"
                    class="status-update-form"
                >


                    <select
                        name="status"
                        class="status-update-select"
                        required
                    >

                        <option
                            value="Pending"
                            <?php
                            if (
                                $complaint['status']
                                == 'Pending'
                            ) {
                                echo 'selected';
                            }
                            ?>
                        >
                            Pending
                        </option>


                        <option
                            value="In Progress"
                            <?php
                            if (
                                $complaint['status']
                                == 'In Progress'
                            ) {
                                echo 'selected';
                            }
                            ?>
                        >
                            In Progress
                        </option>


                        <option
                            value="Resolved"
                            <?php
                            if (
                                $complaint['status']
                                == 'Resolved'
                            ) {
                                echo 'selected';
                            }
                            ?>
                        >
                            Resolved
                        </option>

                    </select>


                    <button
                        type="submit"
                        class="status-update-button"
                    >
                        Update
                    </button>


                </form>

            </div>



            <!-- =================================================
                 BACK BUTTON
            ================================================== -->

            <div class="back-complaints">

                <a href="complaints.php">
                    ← Back to All Complaints
                </a>

            </div>


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