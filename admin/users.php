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
   SEARCH AND FILTER
================================ */

$search = trim($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';


/* ================================
   GET USERS
================================ */

$sql = "
    SELECT id, name, email, phone, role, gender, dob
    FROM users
    WHERE role != 'admin'
";


/* ================================
   SEARCH
================================ */

if ($search != '') {

    $safe_search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $sql .= "
        AND (
            name LIKE '%$safe_search%'
            OR email LIKE '%$safe_search%'
        )
    ";
}


/* ================================
   ROLE FILTER
================================ */

if (
    $role_filter == 'student' ||
    $role_filter == 'teacher'
) {

    $safe_role = mysqli_real_escape_string(
        $conn,
        $role_filter
    );

    $sql .= "
        AND role = '$safe_role'
    ";
}


/* ================================
   ORDER
================================ */

$sql .= "
    ORDER BY id DESC
";


$result = mysqli_query($conn, $sql);


/* ================================
   STORE USERS
================================ */

$users = [];

while ($user = mysqli_fetch_assoc($result)) {
    $users[] = $user;
}

$user_count = count($users);

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
        Users - College Complaint Management System
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >


    <style>

        /* =====================================================
           FORCE THIS PAGE TO USE FULL WIDTH
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
           MAIN PAGE
        ===================================================== */

        .users-main {
            width: 100%;
            max-width: 100%;
            padding: 35px;
            box-sizing: border-box;
        }


        .users-page {
            width: 100%;
            max-width: 1450px;
            margin: 0 auto;
            box-sizing: border-box;
        }


        /* =====================================================
           PAGE TITLE
        ===================================================== */

        .users-welcome {
            width: 100%;
            background: #eaf3ff;
            border-radius: 12px;
            padding: 32px 30px;
            box-sizing: border-box;
            min-height: auto;
        }


        .users-welcome .welcome-icon {
            width: 70px;
            height: 70px;

            background: #ffffff;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 38px;

            margin-bottom: 24px;
        }


        .users-welcome h1 {
            margin: 0;
            color: #174a7c;
            font-size: 30px;
            line-height: 1.25;
        }


        .users-welcome .role {
            margin-top: 12px;
            color: #68839d;
            font-size: 18px;
            line-height: 1.5;
        }


        .users-welcome .welcome-divider {
            width: 100%;
            height: 1px;
            background: #d2dfec;
            margin: 30px 0 25px;
        }


        .users-welcome .welcome-quote {
            color: #31577d;
            font-size: 17px;
            line-height: 1.6;
            font-style: italic;
        }


        /* =====================================================
           SEARCH CARD
        ===================================================== */

        .users-search-card {
            width: 100%;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
            box-sizing: border-box;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);
        }


        .users-search-card h2 {
            margin: 0;
            color: #123f6d;
            text-align: center;
            font-size: 22px;
        }


        .users-search-form {
            width: 100%;
            margin-top: 25px;

            display: grid;

            grid-template-columns:
                minmax(250px, 1fr)
                170px
                120px
                120px;

            gap: 12px;

            box-sizing: border-box;
        }


        .users-search-input {
            width: 100%;
            height: 44px;

            padding: 0 14px;

            border: 1px solid #ccc;
            border-radius: 6px;

            font-size: 15px;

            box-sizing: border-box;
            outline: none;
        }


        .users-search-input:focus {
            border-color: #123f6d;
        }


        .users-role-select {
            width: 100%;
            height: 44px;

            padding: 0 12px;

            border: 1px solid #ccc;
            border-radius: 6px;

            background: white;

            font-size: 15px;

            box-sizing: border-box;
        }


        .users-search-btn {
            width: 100%;
            height: 44px;

            padding: 0 15px;

            border: none;
            border-radius: 6px;

            background: #333;
            color: white;

            font-size: 14px;
            font-weight: bold;

            cursor: pointer;

            box-sizing: border-box;
        }


        .users-search-btn:hover {
            background: #222;
        }


        .users-clear-btn {
            width: 100%;
            height: 44px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 6px;

            background: #e5e5e5;
            color: #333;

            text-decoration: none;

            font-size: 14px;
            font-weight: bold;

            box-sizing: border-box;
        }


        .users-clear-btn:hover {
            background: #d5d5d5;
        }


        /* =====================================================
           USER LIST CARD
        ===================================================== */

        .users-list-card {
            width: 100%;

            background: #ffffff;

            border-radius: 12px;

            padding: 30px;

            margin-top: 30px;

            box-sizing: border-box;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);
        }


        /* =====================================================
           LIST HEADER
        ===================================================== */

        .users-list-header {
            width: 100%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            flex-wrap: wrap;
        }


        .users-list-header h2 {
            margin: 0;
            color: #123f6d;
        }


        .users-count {
            margin: 0;
            color: #666;
            font-size: 14px;
        }


        /* =====================================================
           DESKTOP TABLE
        ===================================================== */

        .users-table-wrapper {
            width: 100%;

            margin-top: 20px;

            overflow-x: auto;

            box-sizing: border-box;
        }


        .users-table {
            width: 100%;

            min-width: 900px;

            border-collapse: collapse;

            table-layout: fixed;

            background: white;
        }


        .users-table th {
            padding: 14px 10px;

            border: 1px solid #ddd;

            background: #f1f1f1;

            color: #123f6d;

            font-size: 14px;

            font-weight: bold;

            text-align: left;

            white-space: nowrap;
        }


        .users-table td {
            padding: 13px 10px;

            border: 1px solid #ddd;

            color: #333;

            font-size: 14px;

            word-break: break-word;

            overflow-wrap: anywhere;

            vertical-align: middle;
        }


        .users-table tbody tr:hover td {
            background: #f8fbff;
        }


        /* Column widths */

        .id-column {
            width: 7%;
            text-align: center !important;
        }


        .name-column {
            width: 17%;
        }


        .email-column {
            width: 23%;
        }


        .phone-column {
            width: 15%;
        }


        .role-column {
            width: 11%;
            text-align: center !important;
        }


        .gender-column {
            width: 11%;
            text-align: center !important;
        }


        .dob-column {
            width: 16%;
        }


        /* =====================================================
           MOBILE CARDS
        ===================================================== */

        .users-mobile-list {
            display: none;
            width: 100%;
            margin-top: 20px;
        }


        .mobile-user-card {
            width: 100%;

            background: #f8fafc;

            border: 1px solid #e1e7ee;

            border-radius: 10px;

            padding: 18px;

            margin-bottom: 15px;

            box-sizing: border-box;
        }


        .mobile-user-card:last-child {
            margin-bottom: 0;
        }


        .mobile-user-top {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            padding-bottom: 13px;

            margin-bottom: 15px;

            border-bottom: 1px solid #e1e7ee;
        }


        .mobile-user-id {
            color: #123f6d;
            font-size: 13px;
            font-weight: bold;
        }


        .mobile-user-role {
            padding: 5px 10px;

            border-radius: 15px;

            background: #eaf4fb;

            color: #123f6d;

            font-size: 11px;

            font-weight: bold;
        }


        .mobile-user-details {
            display: grid;

            grid-template-columns: 1fr;

            gap: 13px;
        }


        .mobile-user-detail {
            display: flex;

            flex-direction: column;

            gap: 4px;
        }


        .mobile-user-label {
            color: #777;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;
        }


        .mobile-user-value {
            color: #333;

            font-size: 14px;

            line-height: 1.5;

            word-break: break-word;

            overflow-wrap: anywhere;
        }


        .no-users {
            padding: 30px;
            text-align: center;
            color: #666;
        }


        .mobile-no-users {
            width: 100%;

            padding: 25px;

            background: #f8fafc;

            border-radius: 8px;

            text-align: center;

            color: #666;

            box-sizing: border-box;
        }


        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 1050px) {

            .users-search-form {
                grid-template-columns:
                    1fr
                    1fr;
            }


            .users-search-input {
                grid-column: 1 / -1;
            }

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 600px) {

            .users-main {
                width: 100%;
                padding: 20px 10px;
            }


            .users-page {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }


            .users-welcome {
                width: 100%;
                padding: 25px 18px;
            }


            .users-welcome h1 {
                font-size: 25px;
            }


            .users-welcome .role {
                font-size: 15px;
            }


            .users-welcome .welcome-quote {
                font-size: 14px;
            }


            .users-search-card,
            .users-list-card {

                width: 100%;

                max-width: 100%;

                padding: 18px;

                margin-left: 0;

                margin-right: 0;

            }


            .users-search-card h2 {
                font-size: 19px;
                line-height: 1.4;
            }


            .users-search-form {

                display: flex;

                flex-direction: column;

                width: 100%;

                gap: 10px;

            }


            .users-search-input,
            .users-role-select,
            .users-search-btn,
            .users-clear-btn {

                width: 100%;

                min-width: 0;

                max-width: 100%;

                flex: none;

            }


            /* Hide table on mobile */

            .users-table-wrapper {
                display: none;
            }


            /* Show cards on mobile */

            .users-mobile-list {
                display: block;
            }


            .users-list-header {
                flex-direction: column;
                align-items: flex-start;
            }


            .mobile-user-card {
                width: 100%;
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

        <a
            href="users.php"
            class="active"
        >
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

<main class="users-main">

    <div class="users-page">


        <!-- =================================================
             PAGE TITLE
        ================================================== -->

        <section class="users-welcome">

            <div class="welcome-icon">
                👥
            </div>

            <h1>
                Registered Users
            </h1>

            <p class="role">
                View students and teachers registered in the system
            </p>

            <div class="welcome-divider"></div>

            <p class="welcome-quote">
                Manage and monitor registered college users.
            </p>

        </section>



        <!-- =================================================
             SEARCH AND FILTER
        ================================================== -->

        <section class="users-search-card">

            <h2>
                Search and Filter Users
            </h2>


            <form
                method="GET"
                action="users.php"
                class="users-search-form"
            >

                <input
                    type="text"
                    name="search"
                    class="users-search-input"
                    placeholder="Search by name or email"
                    value="<?php echo htmlspecialchars($search); ?>"
                >


                <select
                    name="role"
                    class="users-role-select"
                >

                    <option value="">
                        All Roles
                    </option>

                    <option
                        value="student"
                        <?php
                        if ($role_filter == 'student') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Student
                    </option>

                    <option
                        value="teacher"
                        <?php
                        if ($role_filter == 'teacher') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Teacher
                    </option>

                </select>


                <button
                    type="submit"
                    class="users-search-btn"
                >
                    🔍 Search
                </button>


                <a
                    href="users.php"
                    class="users-clear-btn"
                >
                    ✖ Clear
                </a>

            </form>

        </section>



        <!-- =================================================
             USER LIST
        ================================================== -->

        <section class="users-list-card">

            <div class="users-list-header">

                <h2>
                    User List
                </h2>


                <p class="users-count">

                    <?php

                    echo "Showing "
                        . $user_count
                        . " user";

                    if ($user_count != 1) {
                        echo "s";
                    }

                    ?>

                </p>

            </div>



            <!-- =============================================
                 DESKTOP TABLE
            ============================================== -->

            <div class="users-table-wrapper">

                <table class="users-table">

                    <thead>

                        <tr>

                            <th class="id-column">
                                ID
                            </th>

                            <th class="name-column">
                                Name
                            </th>

                            <th class="email-column">
                                Email
                            </th>

                            <th class="phone-column">
                                Phone
                            </th>

                            <th class="role-column">
                                Role
                            </th>

                            <th class="gender-column">
                                Gender
                            </th>

                            <th class="dob-column">
                                Date of Birth
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($user_count > 0): ?>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td class="id-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['id']
                                    );
                                    ?>
                                </td>


                                <td class="name-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['name']
                                    );
                                    ?>
                                </td>


                                <td class="email-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['email']
                                    );
                                    ?>
                                </td>


                                <td class="phone-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['phone']
                                    );
                                    ?>
                                </td>


                                <td class="role-column">
                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $user['role']
                                        )
                                    );
                                    ?>
                                </td>


                                <td class="gender-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['gender']
                                    );
                                    ?>
                                </td>


                                <td class="dob-column">
                                    <?php
                                    echo htmlspecialchars(
                                        $user['dob']
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="no-users"
                            >
                                No users found.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>



            <!-- =============================================
                 MOBILE USER CARDS
            ============================================== -->

            <div class="users-mobile-list">

                <?php if ($user_count > 0): ?>

                    <?php foreach ($users as $user): ?>

                        <div class="mobile-user-card">


                            <div class="mobile-user-top">

                                <span class="mobile-user-id">

                                    User #

                                    <?php
                                    echo htmlspecialchars(
                                        $user['id']
                                    );
                                    ?>

                                </span>


                                <span class="mobile-user-role">

                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $user['role']
                                        )
                                    );
                                    ?>

                                </span>

                            </div>



                            <div class="mobile-user-details">


                                <div class="mobile-user-detail">

                                    <span class="mobile-user-label">
                                        Name
                                    </span>

                                    <span class="mobile-user-value">
                                        <?php
                                        echo htmlspecialchars(
                                            $user['name']
                                        );
                                        ?>
                                    </span>

                                </div>


                                <div class="mobile-user-detail">

                                    <span class="mobile-user-label">
                                        Email
                                    </span>

                                    <span class="mobile-user-value">
                                        <?php
                                        echo htmlspecialchars(
                                            $user['email']
                                        );
                                        ?>
                                    </span>

                                </div>


                                <div class="mobile-user-detail">

                                    <span class="mobile-user-label">
                                        Phone
                                    </span>

                                    <span class="mobile-user-value">
                                        <?php
                                        echo htmlspecialchars(
                                            $user['phone']
                                        );
                                        ?>
                                    </span>

                                </div>


                                <div class="mobile-user-detail">

                                    <span class="mobile-user-label">
                                        Gender
                                    </span>

                                    <span class="mobile-user-value">
                                        <?php
                                        echo htmlspecialchars(
                                            $user['gender']
                                        );
                                        ?>
                                    </span>

                                </div>


                                <div class="mobile-user-detail">

                                    <span class="mobile-user-label">
                                        Date of Birth
                                    </span>

                                    <span class="mobile-user-value">
                                        <?php
                                        echo htmlspecialchars(
                                            $user['dob']
                                        );
                                        ?>
                                    </span>

                                </div>


                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="mobile-no-users">
                        No users found.
                    </div>

                <?php endif; ?>

            </div>


        </section>


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