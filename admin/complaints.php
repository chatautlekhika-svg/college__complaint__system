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
   ARCHIVE RESOLVED COMPLAINT
================================ */

if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    &&
    ($_POST['action'] ?? '') == 'archive'
) {

    $complaint_id = (int)($_POST['complaint_id'] ?? 0);


    if ($complaint_id > 0) {

        $sql_archive = "
            UPDATE complaints
            SET archived = 1
            WHERE id = ?
            AND status = 'Resolved'
        ";

        $stmt = $conn->prepare($sql_archive);

        $stmt->bind_param(
            "i",
            $complaint_id
        );

        $stmt->execute();

        $stmt->close();
    }


    /* Keep existing search/filter/page */

    $redirect_params = [];

    if (!empty($_POST['search'])) {
        $redirect_params['search'] = $_POST['search'];
    }

    if (!empty($_POST['status'])) {
        $redirect_params['status'] = $_POST['status'];
    }

    if (!empty($_POST['role'])) {
        $redirect_params['role'] = $_POST['role'];
    }

    if (!empty($_POST['category'])) {
        $redirect_params['category'] = $_POST['category'];
    }

    if (!empty($_POST['page'])) {
        $redirect_params['page'] = (int)$_POST['page'];
    }


    $redirect_url = "complaints.php";

    if (!empty($redirect_params)) {

        $redirect_url .= "?"
            . http_build_query(
                $redirect_params
            );

    }


    header("Location: " . $redirect_url);
    exit();
}


/* ================================
   SEARCH AND FILTER
================================ */

$search = trim($_GET['search'] ?? '');

$status_filter = $_GET['status'] ?? '';

$role_filter = $_GET['role'] ?? '';

$category_filter = $_GET['category'] ?? '';


/* ================================
   PAGINATION
================================ */

$records_per_page = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;


if ($page < 1) {
    $page = 1;
}


/* ================================
   BASE QUERY
================================ */

$where_sql = "
    FROM complaints
    INNER JOIN users
        ON complaints.user_id = users.id
    WHERE complaints.archived = 0
";


/* ================================
   SEARCH
================================ */

if ($search != '') {

    $search_safe = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where_sql .= "
        AND (
            users.name LIKE '%$search_safe%'
            OR complaints.subject LIKE '%$search_safe%'
        )
    ";
}


/* ================================
   STATUS FILTER
================================ */

if ($status_filter != '') {

    $status_safe = mysqli_real_escape_string(
        $conn,
        $status_filter
    );

    $where_sql .= "
        AND complaints.status = '$status_safe'
    ";
}


/* ================================
   ROLE FILTER
================================ */

if ($role_filter != '') {

    $role_safe = mysqli_real_escape_string(
        $conn,
        $role_filter
    );

    $where_sql .= "
        AND users.role = '$role_safe'
    ";
}


/* ================================
   CATEGORY FILTER
================================ */

if ($category_filter != '') {

    $category_safe = mysqli_real_escape_string(
        $conn,
        $category_filter
    );

    $where_sql .= "
        AND complaints.category = '$category_safe'
    ";
}


/* ================================
   COUNT
================================ */

$count_sql = "
    SELECT COUNT(*) AS total
    $where_sql
";

$count_result = mysqli_query(
    $conn,
    $count_sql
);

$total_complaints =
    mysqli_fetch_assoc(
        $count_result
    )['total'];


/* ================================
   TOTAL PAGES
================================ */

$total_pages = max(
    1,
    (int)ceil(
        $total_complaints
        /
        $records_per_page
    )
);


if ($page > $total_pages) {
    $page = $total_pages;
}


/* ================================
   OFFSET
================================ */

$offset =
    ($page - 1)
    *
    $records_per_page;


/* ================================
   GET COMPLAINTS
================================ */

$sql = "
    SELECT
        complaints.id,
        complaints.subject,
        complaints.category,
        complaints.description,
        complaints.status,
        complaints.created_at,
        complaints.archived,
        users.name,
        users.email,
        users.role
    $where_sql

    ORDER BY complaints.created_at DESC

    LIMIT $offset, $records_per_page
";


$result = mysqli_query(
    $conn,
    $sql
);


$current_count =
    mysqli_num_rows($result);


/* ================================
   DISPLAY RANGE
================================ */

if ($total_complaints > 0) {

    $start_record =
        $offset + 1;

    $end_record =
        $offset + $current_count;

} else {

    $start_record = 0;

    $end_record = 0;
}


/* ================================
   PAGINATION URL
================================ */

function pagination_url(
    $page_number
) {

    $params = $_GET;

    $params['page'] =
        $page_number;

    return 'complaints.php?'
        .
        http_build_query(
            $params
        );
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
        All Complaints - College Complaint Management System
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >

    <style>

        .complaints-wrapper {
            display: block !important;
            width: 100%;
            box-sizing: border-box;
        }


        .complaints-top-row {

            width: 100%;

            display: grid;

            grid-template-columns:
                0.7fr 1.3fr;

            gap: 30px;

            align-items: stretch;

        }


        .complaints-welcome {

            min-width: 0;

            box-sizing: border-box;

        }


        .complaints-filter-card {

            min-width: 0;

            box-sizing: border-box;

        }


        .complaint-filter-form {

            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

            margin-top: 20px;

        }


        .complaint-search {

            flex: 1;

            min-width: 220px;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            box-sizing: border-box;

            font-size: 14px;

        }


        .complaint-filter-select {

            min-width: 140px;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            background-color: white;

            font-size: 14px;

            box-sizing: border-box;

        }


        .complaint-search-btn {

            padding: 12px 18px;

            border: none;

            border-radius: 6px;

            background-color: #333;

            color: white;

            cursor: pointer;

            font-weight: bold;

        }


        .complaint-clear-btn {

            padding: 12px 18px;

            border: 1px solid #ccc;

            border-radius: 6px;

            background-color: #f5f5f5;

            color: #333;

            text-decoration: none;

            font-weight: bold;

        }


        .complaint-list-card {

            width: 100%;

            margin-top: 30px;

            box-sizing: border-box;

            overflow-x: auto;

        }


        .complaint-list-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            flex-wrap: wrap;

        }


        .complaint-count {

            margin: 0;

            color: #666;

            font-size: 14px;

        }


        .complaint-table-wrapper {

            width: 100%;

            overflow-x: auto;

            margin-top: 20px;

        }


        .complaint-table {

            width: 100%;

            min-width: 1100px;

            border-collapse: collapse;

            table-layout: fixed;

        }


        .complaint-table th {

            padding: 13px 10px;

            border: 1px solid #ddd;

            background-color: #f1f1f1;

            color: #123f6d;

            font-size: 14px;

            font-weight: bold;

            text-align: left;

            white-space: nowrap;

        }


        .complaint-table td {

            padding: 12px 10px;

            border: 1px solid #ddd;

            color: #333;

            font-size: 14px;

            vertical-align: middle;

            word-break: break-word;

        }


        .complaint-table tbody tr:hover td {

            background-color: #f8fbff;

        }


        .id-column {
            width: 5%;
            text-align: center !important;
        }


        .user-column {
            width: 17%;
        }


        .role-column {
            width: 8%;
            text-align: center !important;
        }


        .subject-column {
            width: 16%;
        }


        .category-column {
            width: 12%;
        }


        .status-column {
            width: 10%;
            text-align: center !important;
        }


        .action-column {
            width: 20%;
            text-align: center !important;
        }


        .date-column {
            width: 12%;
        }


        .status-badge {

            display: inline-block;

            padding: 6px 11px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;

            white-space: nowrap;

        }


        .status-pending {

            background-color: #fff3cd;

            color: #856404;

        }


        .status-progress {

            background-color: #dbeafe;

            color: #1e40af;

        }


        .status-resolved {

            background-color: #d1fae5;

            color: #065f46;

        }


        .complaint-actions {

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 8px;

            flex-wrap: wrap;

        }


        .view-details-btn {

            display: inline-block;

            padding: 8px 12px;

            border: 1px solid #123f6d;

            border-radius: 5px;

            background-color: white;

            color: #123f6d;

            cursor: pointer;

            font-size: 12px;

            font-weight: bold;

            text-decoration: none;

        }


        .view-details-btn:hover {

            background-color: #123f6d;

            color: white;

        }


        .status-form {

            display: flex;

            align-items: center;

            gap: 6px;

            flex-wrap: wrap;

            justify-content: center;

        }


        .status-select {

            width: 120px;

            padding: 7px;

            border: 1px solid #ccc;

            border-radius: 5px;

            background-color: white;

            font-size: 12px;

        }


        /*
           Status dropdown remains visible on every row.

           Actual status update is now done on the
           View Complaint Details page.
        */

        .status-select:disabled {

            color: #555;

            background-color: #f8f8f8;

            cursor: not-allowed;

        }


        .archive-btn {

            padding: 7px 10px;

            border: none;

            border-radius: 5px;

            background-color: #6b7280;

            color: white;

            cursor: pointer;

            font-size: 12px;

            font-weight: bold;

        }


        .archive-btn:hover {

            background-color: #4b5563;

        }


        /* =====================================================
           PAGINATION
        ===================================================== */

        .pagination-wrapper {

            margin-top: 25px;

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 7px;

            flex-wrap: wrap;

        }


        .pagination-link {

            min-width: 36px;

            height: 36px;

            padding: 0 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            box-sizing: border-box;

            border: 1px solid #ccc;

            border-radius: 6px;

            background-color: white;

            color: #333;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

        }


        .pagination-link:hover {

            background-color: #f1f1f1;

        }


        .pagination-link.active {

            background-color: #123f6d;

            color: white;

            border-color: #123f6d;

        }


        .pagination-link.disabled {

            color: #aaa;

            background-color: #f5f5f5;

            pointer-events: none;

        }


        .pagination-dots {

            padding: 0 4px;

            color: #777;

            font-weight: bold;

        }


        @media (max-width: 1000px) {

            .complaints-top-row {

                grid-template-columns: 1fr;

            }


            .complaint-search {

                min-width: 100%;

                flex: none;

            }


            .complaint-filter-select {

                flex: 1;

            }

        }


        @media (max-width: 700px) {

            .complaint-filter-select {

                width: 100%;

                flex: none;

            }


            .complaint-search-btn,
            .complaint-clear-btn {

                flex: 1;

                text-align: center;

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

<main class="dashboard-main">

    <div class="dashboard-wrapper complaints-wrapper">


        <!-- =================================================
             TOP ROW
        ================================================== -->

        <div class="complaints-top-row">


            <div class="welcome-panel complaints-welcome">

                <div class="welcome-icon">
                    📋
                </div>

                <h1>
                    All Complaints
                </h1>

                <p class="role">
                    Manage complaints submitted by students and teachers
                </p>

                <div class="welcome-divider"></div>

                <p class="welcome-quote">
                    Review complaints and keep track of their status.
                </p>

            </div>



            <div class="dashboard-card complaints-filter-card">

                <h2>
                    Search and Filter Complaints
                </h2>


                <form
                    method="GET"
                    class="complaint-filter-form"
                >


                    <input
                        type="text"
                        name="search"
                        class="complaint-search"
                        placeholder="Search by subject or name"
                        value="<?php echo htmlspecialchars($search); ?>"
                    >


                    <select
                        name="status"
                        class="complaint-filter-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="Pending"
                            <?php
                            if ($status_filter == 'Pending') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Pending
                        </option>

                        <option
                            value="In Progress"
                            <?php
                            if ($status_filter == 'In Progress') {
                                echo 'selected';
                            }
                            ?>
                        >
                            In Progress
                        </option>

                        <option
                            value="Resolved"
                            <?php
                            if ($status_filter == 'Resolved') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Resolved
                        </option>

                    </select>


                    <select
                        name="role"
                        class="complaint-filter-select"
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


                    <select
                        name="category"
                        class="complaint-filter-select"
                    >

                        <option value="">
                            All Categories
                        </option>

                        <option value="Academic"
                            <?php
                            if ($category_filter == 'Academic') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Academic
                        </option>

                        <option value="Infrastructure"
                            <?php
                            if ($category_filter == 'Infrastructure') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Infrastructure
                        </option>

                        <option value="Faculty"
                            <?php
                            if ($category_filter == 'Faculty') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Faculty
                        </option>

                        <option value="Examination"
                            <?php
                            if ($category_filter == 'Examination') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Examination
                        </option>

                        <option value="Library"
                            <?php
                            if ($category_filter == 'Library') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Library
                        </option>

                        <option value="IT / Computer"
                            <?php
                            if ($category_filter == 'IT / Computer') {
                                echo 'selected';
                            }
                            ?>
                        >
                            IT / Computer
                        </option>

                        <option value="Cleanliness"
                            <?php
                            if ($category_filter == 'Cleanliness') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Cleanliness
                        </option>
                        <option value="Canteen"
                        <?php
                            if ($category_filter == 'Canteen') {
                                echo 'selected';
                            }
                            ?>
                            >
                            Canteen</option>

                        <option value="Other"
                            <?php
                            if ($category_filter == 'Other') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Other
                        </option>

                    </select>


                    <button
                        type="submit"
                        class="complaint-search-btn"
                    >
                        🔎 Search
                    </button>


                    <a
                        href="complaints.php"
                        class="complaint-clear-btn"
                    >
                        Clear
                    </a>


                </form>

            </div>


        </div>



        <!-- =================================================
             COMPLAINT LIST
        ================================================== -->

        <div class="dashboard-card complaint-list-card">


            <div class="complaint-list-header">

                <h2>
                    Complaint List
                </h2>


                <p class="complaint-count">

                    <?php

                    if ($total_complaints > 0) {

                        echo "Showing "
                            . $start_record
                            . "-"
                            . $end_record
                            . " of "
                            . $total_complaints
                            . " complaints";

                    } else {

                        echo "Showing 0 complaints";

                    }

                    ?>

                </p>

            </div>



            <div class="complaint-table-wrapper">

                <table class="complaint-table">

                    <thead>

                        <tr>

                            <th class="id-column">
                                ID
                            </th>

                            <th class="user-column">
                                Submitted By
                            </th>

                            <th class="role-column">
                                Role
                            </th>

                            <th class="subject-column">
                                Subject
                            </th>

                            <th class="category-column">
                                Category
                            </th>

                            <th class="status-column">
                                Status
                            </th>

                            <th class="action-column">
                                Actions
                            </th>

                            <th class="date-column">
                                Date
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if ($current_count > 0): ?>


                        <?php while (
                            $complaint =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <?php

                            if (
                                $complaint['status']
                                == 'Pending'
                            ) {

                                $status_class =
                                    'status-pending';

                            } elseif (
                                $complaint['status']
                                == 'In Progress'
                            ) {

                                $status_class =
                                    'status-progress';

                            } else {

                                $status_class =
                                    'status-resolved';

                            }

                            ?>


                            <tr>


                                <td class="id-column">

                                    <?php
                                    echo htmlspecialchars(
                                        $complaint['id']
                                    );
                                    ?>

                                </td>



                                <td class="user-column">

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $complaint['name']
                                        );
                                        ?>

                                    </strong>

                                    <br>

                                    <small style="color:#666;">

                                        <?php
                                        echo htmlspecialchars(
                                            $complaint['email']
                                        );
                                        ?>

                                    </small>

                                </td>



                                <td class="role-column">

                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $complaint['role']
                                        )
                                    );
                                    ?>

                                </td>



                                <td class="subject-column">

                                    <?php
                                    echo htmlspecialchars(
                                        $complaint['subject']
                                    );
                                    ?>

                                </td>



                                <td class="category-column">

                                    <?php
                                    echo htmlspecialchars(
                                        $complaint['category']
                                    );
                                    ?>

                                </td>



                                <td class="status-column">

                                    <span
                                        class="
                                            status-badge
                                            <?php
                                            echo $status_class;
                                            ?>
                                        "
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $complaint['status']
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- =================================
                                     ACTIONS
                                ================================== -->

                                <td class="action-column">

                                    <div class="complaint-actions">


                                        <!-- VIEW -->

                                        <a
                                            href="view_complaint.php?id=<?php echo $complaint['id']; ?>"
                                            class="view-details-btn"
                                        >
                                            👁 View
                                        </a>



                                        <!-- STATUS DROPDOWN -->

                                        <select
                                            class="status-select"
                                            disabled
                                            title="Open View to update complaint status"
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



                                        <!-- ARCHIVE ONLY WHEN RESOLVED -->

                                        <?php if (
                                            $complaint['status']
                                            == 'Resolved'
                                        ): ?>

                                            <form
                                                method="POST"
                                                style="display:inline;"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="archive"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="complaint_id"
                                                    value="<?php echo $complaint['id']; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="search"
                                                    value="<?php echo htmlspecialchars($search); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="<?php echo htmlspecialchars($status_filter); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="role"
                                                    value="<?php echo htmlspecialchars($role_filter); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="category"
                                                    value="<?php echo htmlspecialchars($category_filter); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="page"
                                                    value="<?php echo $page; ?>"
                                                >


                                                <button
                                                    type="submit"
                                                    class="archive-btn"
                                                    onclick="return confirm('Are you sure you want to archive this resolved complaint?');"
                                                >
                                                    🗄 Archive
                                                </button>

                                            </form>

                                        <?php endif; ?>


                                    </div>

                                </td>



                                <td class="date-column">

                                    <?php
                                    echo htmlspecialchars(
                                        $complaint['created_at']
                                    );
                                    ?>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="8"
                                style="
                                    padding:30px;
                                    text-align:center;
                                    color:#666;
                                "
                            >
                                No complaints found.
                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>



            <!-- =================================================
                 PAGINATION
            ================================================== -->

            <?php if ($total_pages > 1): ?>

                <div class="pagination-wrapper">


                    <?php if ($page > 1): ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                pagination_url(
                                    $page - 1
                                )
                            ); ?>"
                            class="pagination-link"
                        >
                            ← Previous
                        </a>

                    <?php else: ?>

                        <span
                            class="pagination-link disabled"
                        >
                            ← Previous
                        </span>

                    <?php endif; ?>



                    <?php

                    if (
                        $total_pages <= 7
                    ):

                        for (
                            $i = 1;
                            $i <= $total_pages;
                            $i++
                        ):

                    ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                pagination_url($i)
                            ); ?>"
                            class="
                                pagination-link
                                <?php
                                echo (
                                    $i == $page
                                )
                                    ? 'active'
                                    : '';
                                ?>
                            "
                        >
                            <?php echo $i; ?>
                        </a>

                    <?php

                        endfor;

                    else:

                    ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                pagination_url(1)
                            ); ?>"
                            class="
                                pagination-link
                                <?php
                                echo (
                                    $page == 1
                                )
                                    ? 'active'
                                    : '';
                                ?>
                            "
                        >
                            1
                        </a>


                        <?php if ($page > 3): ?>

                            <span
                                class="pagination-dots"
                            >
                                ...
                            </span>

                        <?php endif; ?>


                        <?php

                        $start_page =
                            max(
                                2,
                                $page - 1
                            );

                        $end_page =
                            min(
                                $total_pages - 1,
                                $page + 1
                            );


                        for (
                            $i = $start_page;
                            $i <= $end_page;
                            $i++
                        ):

                        ?>

                            <a
                                href="<?php echo htmlspecialchars(
                                    pagination_url($i)
                                ); ?>"
                                class="
                                    pagination-link
                                    <?php
                                    echo (
                                        $i == $page
                                    )
                                        ? 'active'
                                        : '';
                                    ?>
                                "
                            >
                                <?php echo $i; ?>
                            </a>

                        <?php endfor; ?>


                        <?php if (
                            $page
                            <
                            $total_pages - 2
                        ): ?>

                            <span
                                class="pagination-dots"
                            >
                                ...
                            </span>

                        <?php endif; ?>


                        <a
                            href="<?php echo htmlspecialchars(
                                pagination_url(
                                    $total_pages
                                )
                            ); ?>"
                            class="
                                pagination-link
                                <?php
                                echo (
                                    $page
                                    ==
                                    $total_pages
                                )
                                    ? 'active'
                                    : '';
                                ?>
                            "
                        >
                            <?php echo $total_pages; ?>
                        </a>


                    <?php endif; ?>



                    <?php if (
                        $page
                        <
                        $total_pages
                    ): ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                pagination_url(
                                    $page + 1
                                )
                            ); ?>"
                            class="pagination-link"
                        >
                            Next →
                        </a>

                    <?php else: ?>

                        <span
                            class="pagination-link disabled"
                        >
                            Next →
                        </span>

                    <?php endif; ?>


                </div>

            <?php endif; ?>


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