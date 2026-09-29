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
   CHECK STUDENT ROLE
================================ */

if ($_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}


/* ================================
   GET STUDENT COMPLAINTS
================================ */

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
        id,
        subject,
        category,
        description,
        status,
        created_at
    FROM complaints
    WHERE user_id = ?
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

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
        My Complaints - Student
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/complaints.css"
    >

    <style>

        /* ==========================================
           PAGE LAYOUT
        ========================================== */

        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }


        .my-complaints-container {

            width: calc(100% - 80px);

            max-width: 1200px;

            margin: 0 auto;

            box-sizing: border-box;

        }


        /* ==========================================
           COMPLAINT TABLE CARD
        ========================================== */

        .my-complaints-card {

            margin-top: 30px;

            width: 100%;

            background-color: #ffffff;

            border-radius: 12px;

            padding: 30px;

            box-sizing: border-box;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);

        }


        .complaints-list-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            flex-wrap: wrap;

            gap: 10px;

        }


        .complaints-list-header h2 {

            margin: 0;

        }


        .complaints-count {

            margin: 0;

            color: #666;

            font-size: 14px;

        }


        /* ==========================================
           TABLE
        ========================================== */

        .my-complaints-table-wrapper {

            width: 100%;

            overflow-x: auto;

            margin-top: 20px;

        }


        .my-complaints-table {

            width: 100%;

            min-width: 850px;

            border-collapse: collapse;

            background-color: #ffffff;

        }


        .my-complaints-table th {

            padding: 14px 12px;

            border: 1px solid #ddd;

            background-color: #f1f1f1;

            color: #123f6d;

            font-size: 14px;

            font-weight: bold;

            text-align: left;

            white-space: nowrap;

        }


        .my-complaints-table td {

            padding: 13px 12px;

            border: 1px solid #ddd;

            font-size: 14px;

            color: #333;

            vertical-align: middle;

        }


        .my-complaints-table tbody tr:hover td {

            background-color: #f8fbff;

        }


        /* ==========================================
           COLUMN WIDTHS
        ========================================== */

        .complaint-id {

            width: 7%;

            text-align: center;

        }


        .complaint-subject {

            width: 28%;

        }


        .complaint-category {

            width: 20%;

        }


        .complaint-date {

            width: 17%;

        }


        .complaint-status {

            width: 15%;

            text-align: center;

        }


        .complaint-action {

            width: 13%;

            text-align: center;

        }


        /* ==========================================
           STATUS BADGES
        ========================================== */

        .status {

            display: inline-block;

            padding: 7px 12px;

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


        /* ==========================================
           VIEW BUTTON
        ========================================== */

        .view-complaint-btn {

            padding: 8px 12px;

            border: 1px solid #123f6d;

            border-radius: 5px;

            background-color: white;

            color: #123f6d;

            cursor: pointer;

            font-size: 12px;

            font-weight: bold;

        }


        .view-complaint-btn:hover {

            background-color: #123f6d;

            color: white;

        }


        /* ==========================================
           NO COMPLAINTS
        ========================================== */

        .no-complaints-box {

            margin-top: 30px;

            padding: 45px 25px;

            text-align: center;

            background-color: #ffffff;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);

        }


        .no-complaints-icon {

            font-size: 45px;

            margin-bottom: 15px;

        }


        .no-complaints-box p {

            margin: 0;

            color: #666;

            font-size: 15px;

        }


        /* ==========================================
           ACTION BUTTONS
        ========================================== */

        .complaints-actions {

            margin-top: 30px;

            display: flex;

            justify-content: center;

            gap: 15px;

            flex-wrap: wrap;

        }


        /* ==========================================
           MODAL
        ========================================== */

        .complaint-modal {

            display: none;

            position: fixed;

            z-index: 9999;

            left: 0;

            top: 0;

            width: 100%;

            height: 100%;

            background-color: rgba(0, 0, 0, 0.55);

            align-items: center;

            justify-content: center;

            padding: 20px;

            box-sizing: border-box;

        }


        .complaint-modal-content {

            position: relative;

            width: 700px;

            max-width: 100%;

            max-height: 90vh;

            overflow-y: auto;

            background-color: #ffffff;

            border-radius: 12px;

            padding: 30px;

            box-sizing: border-box;

        }


        .modal-close {

            position: absolute;

            top: 10px;

            right: 18px;

            font-size: 28px;

            color: #666;

            cursor: pointer;

            font-weight: bold;

        }


        .modal-close:hover {

            color: #111;

        }


        .modal-title {

            margin: 0 35px 25px 0;

            color: #123f6d;

            font-size: 24px;

        }


        .modal-info-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;

        }


        .modal-info-item {

            padding: 13px;

            background-color: #f7f9fc;

            border-radius: 6px;

        }


        .modal-info-label {

            display: block;

            margin-bottom: 5px;

            color: #666;

            font-size: 12px;

            font-weight: bold;

        }


        .modal-description {

            margin-top: 18px;

            padding: 15px;

            background-color: #f7f9fc;

            border-radius: 6px;

            line-height: 1.6;

            white-space: pre-wrap;

        }


        .modal-description-label {

            display: block;

            margin-bottom: 8px;

            color: #123f6d;

            font-weight: bold;

        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 900px) {

            .my-complaints-container {

                width: calc(100% - 40px);

            }

        }


        @media (max-width: 700px) {

            .my-complaints-container {

                width: calc(100% - 20px);

            }


            .my-complaints-card {

                padding: 20px;

            }


            .modal-info-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     HEADER
========================================== -->

<header class="complaints-header">

    <div class="complaints-brand">

        <img
            src="../assets/images/mbmc-logo.png"
            class="complaints-logo"
            alt="MBMC Logo"
        >

        <div class="complaints-brand-text">

            <h2>
                MADAN BHANDARI MEMORIAL COLLEGE
            </h2>

            <p>
                College Complaint Management System
            </p>

        </div>

    </div>


    <nav class="complaints-nav">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="submit_complaint.php">
            📝 Submit Complaint
        </a>

        <a
            href="my_complaints.php"
            class="active"
        >
            📋 My Complaints
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a
            href="../logout.php"
            class="logout"
        >
            🚪 Logout
        </a>

    </nav>

</header>


<!-- ==========================================
     MAIN
========================================== -->

<main class="complaints-main">

    <div class="my-complaints-container">


        <!-- ==========================================
             PAGE TITLE
        ========================================== -->

        <div class="complaints-title">

            <div class="complaints-title-icon">
                📋
            </div>

            <div>

                <h1>
                    My Complaints
                </h1>

                <p>
                    View the complaints you have submitted and check their status.
                </p>

            </div>

        </div>


        <?php if ($result->num_rows > 0) { ?>


            <!-- ==========================================
                 COMPLAINT LIST
            ========================================== -->

            <div class="my-complaints-card">


                <div class="complaints-list-header">

                    <h2>
                        Complaint History
                    </h2>


                    <p class="complaints-count">

                        <?php

                        $complaint_count = $result->num_rows;

                        echo "Showing "
                            . $complaint_count
                            . " complaint";

                        if ($complaint_count != 1) {
                            echo "s";
                        }

                        ?>

                    </p>

                </div>


                <div class="my-complaints-table-wrapper">

                    <table class="my-complaints-table">

                        <thead>

                            <tr>

                                <th class="complaint-id">
                                    ID
                                </th>

                                <th class="complaint-subject">
                                    Subject
                                </th>

                                <th class="complaint-category">
                                    Category
                                </th>

                                <th class="complaint-date">
                                    Submitted Date
                                </th>

                                <th class="complaint-status">
                                    Status
                                </th>

                                <th class="complaint-action">
                                    Details
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php while ($row = $result->fetch_assoc()) { ?>


                            <?php

                            if ($row['status'] == 'Pending') {

                                $status_class =
                                    'status status-pending';

                            } elseif (
                                $row['status'] == 'In Progress'
                            ) {

                                $status_class =
                                    'status status-progress';

                            } elseif (
                                $row['status'] == 'Resolved'
                            ) {

                                $status_class =
                                    'status status-resolved';

                            } else {

                                $status_class =
                                    'status status-pending';

                            }

                            ?>


                            <tr>


                                <!-- ID -->

                                <td class="complaint-id">

                                    #<?php
                                    echo $row['id'];
                                    ?>

                                </td>


                                <!-- SUBJECT -->

                                <td class="complaint-subject">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['subject']
                                    );
                                    ?>

                                </td>


                                <!-- CATEGORY -->

                                <td class="complaint-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['category']
                                    );
                                    ?>

                                </td>


                                <!-- DATE -->

                                <td class="complaint-date">

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $row['created_at']
                                        )
                                    );
                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td class="complaint-status">

                                    <span class="<?php echo $status_class; ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['status']
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- DETAILS -->

                                <td class="complaint-action">

                                    <button
                                        type="button"
                                        class="view-complaint-btn"
                                        data-subject="<?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?>"
                                        data-category="<?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?>"
                                        data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                                        data-status="<?php echo htmlspecialchars($row['status'], ENT_QUOTES); ?>"
                                        data-date="<?php echo htmlspecialchars(date('d M Y', strtotime($row['created_at'])), ENT_QUOTES); ?>"
                                        onclick="openComplaintDetails(this)"
                                    >
                                        👁 View
                                    </button>

                                </td>


                            </tr>


                        <?php } ?>


                        </tbody>

                    </table>

                </div>

            </div>


        <?php } else { ?>


            <!-- ==========================================
                 NO COMPLAINTS
            ========================================== -->

            <div class="no-complaints-box">

                <div class="no-complaints-icon">
                    📋
                </div>

                <p>
                    You have not submitted any complaints yet.
                </p>

            </div>


        <?php } ?>


        <!-- ==========================================
             ACTION BUTTONS
        ========================================== -->

        <div class="complaints-actions">

            <a
                href="submit_complaint.php"
                class="complaints-button new-complaint-btn"
            >
                📝 Submit New Complaint
            </a>

            <a
                href="dashboard.php"
                class="complaints-button back-dashboard-btn"
            >
                ← Back to Dashboard
            </a>

        </div>


    </div>

</main>


<!-- ==========================================
     COMPLAINT DETAILS MODAL
========================================== -->

<div
    id="complaintModal"
    class="complaint-modal"
    onclick="closeComplaintDetails(event)"
>

    <div
        class="complaint-modal-content"
        onclick="event.stopPropagation()"
    >

        <span
            class="modal-close"
            onclick="closeComplaintDetails()"
        >
            ×
        </span>


        <h2
            id="modalSubject"
            class="modal-title"
        >
            Complaint Details
        </h2>


        <div class="modal-info-grid">


            <div class="modal-info-item">

                <span class="modal-info-label">
                    Category
                </span>

                <span id="modalCategory"></span>

            </div>


            <div class="modal-info-item">

                <span class="modal-info-label">
                    Submitted Date
                </span>

                <span id="modalDate"></span>

            </div>


            <div class="modal-info-item">

                <span class="modal-info-label">
                    Status
                </span>

                <span id="modalStatus"></span>

            </div>


        </div>


        <div class="modal-description">

            <span class="modal-description-label">
                Complaint Description
            </span>

            <span id="modalDescription"></span>

        </div>


    </div>

</div>


<!-- ==========================================
     JAVASCRIPT
========================================== -->

<script>

function openComplaintDetails(button) {

    document.getElementById("modalSubject").textContent =
        button.dataset.subject;

    document.getElementById("modalCategory").textContent =
        button.dataset.category;

    document.getElementById("modalDate").textContent =
        button.dataset.date;

    document.getElementById("modalStatus").textContent =
        button.dataset.status;

    document.getElementById("modalDescription").textContent =
        button.dataset.description;

    document.getElementById("complaintModal").style.display =
        "flex";
}


function closeComplaintDetails(event) {

    if (
        !event ||
        event.target.id === "complaintModal" ||
        event.target.classList.contains("modal-close")
    ) {

        document.getElementById("complaintModal").style.display =
            "none";
    }

}


document.addEventListener(
    "keydown",
    function(event) {

        if (event.key === "Escape") {

            document.getElementById("complaintModal").style.display =
                "none";

        }

    }
);

</script>


</body>

</html>