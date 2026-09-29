<?php

session_start();

include "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get teacher complaints */
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

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Complaints - Teacher</title>

    <link rel="stylesheet" href="../assets/css/complaints.css">

    <style>

        .my-complaints-container {
            width: calc(100% - 80px);
            max-width: 1200px;
            margin: 0 auto;
        }

        .my-complaints-card {
            margin-top: 30px;
            width: 100%;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        .my-complaints-table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        .my-complaints-table th,
        .my-complaints-table td {
            padding: 16px 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .my-complaints-table th {
            background: #f5f7fa;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .my-complaints-table td {
            font-size: 14px;
            color: #4b5563;
            vertical-align: middle;
        }

        .my-complaints-table th:nth-child(1) {
            width: 7%;
        }

        .my-complaints-table th:nth-child(2) {
            width: 28%;
        }

        .my-complaints-table th:nth-child(3) {
            width: 20%;
        }

        .my-complaints-table th:nth-child(4) {
            width: 17%;
        }

        .my-complaints-table th:nth-child(5) {
            width: 15%;
        }

        .my-complaints-table th:nth-child(6) {
            width: 13%;
        }

        .view-details-btn {
            display: inline-block;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .view-details-btn:hover {
            background: #1d4ed8;
        }

        .no-complaints-card {
            margin-top: 30px;
            background: #ffffff;
            border-radius: 12px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .no-complaints-card .no-complaints-icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .no-complaints-card p {
            margin: 0;
            color: #6b7280;
            font-size: 15px;
        }

        /* Modal */

        .complaint-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .complaint-modal-content {
            width: 700px;
            max-width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }

        .complaint-modal-content h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #1f2937;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #111827;
        }

        .detail-row {
            margin-bottom: 18px;
        }

        .detail-row strong {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-size: 14px;
        }

        .detail-row span,
        .detail-description {
            display: block;
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
        }

        .detail-description {
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {

            .my-complaints-container {
                width: calc(100% - 30px);
            }

            .my-complaints-card {
                padding: 20px;
            }

            .complaint-modal-content {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>


<header class="complaints-header">

    <div class="complaints-brand">

        <img
            src="../assets/images/mbmc-logo.png"
            class="complaints-logo"
            alt="MBMC Logo"
        >

        <div class="complaints-brand-text">

            <h2>MADAN BHANDARI MEMORIAL COLLEGE</h2>

            <p>College Complaint Management System</p>

        </div>

    </div>


    <nav class="complaints-nav">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="submit_complaint.php">
            📝 Submit Complaint
        </a>

        <a href="my_complaints.php" class="active">
            📋 My Complaints
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a href="../logout.php" class="logout">
            🚪 Logout
        </a>

    </nav>

</header>


<main class="complaints-main">

    <div class="my-complaints-container">


        <!-- Page Title -->

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


            <!-- Complaint List Card -->

            <div class="my-complaints-card">

                <table class="my-complaints-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Subject</th>

                            <th>Category</th>

                            <th>Submitted Date</th>

                            <th>Status</th>

                            <th>Details</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                #<?php echo $row['id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['subject']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['category']); ?>
                            </td>

                            <td>
                                <?php echo date("d M Y", strtotime($row['created_at'])); ?>
                            </td>

                            <td>

                                <?php

                                if ($row['status'] == 'Pending') {

                                    echo '<span class="status status-pending">
                                            Pending
                                          </span>';

                                } elseif ($row['status'] == 'In Progress') {

                                    echo '<span class="status status-progress">
                                            In Progress
                                          </span>';

                                } elseif ($row['status'] == 'Resolved') {

                                    echo '<span class="status status-resolved">
                                            Resolved
                                          </span>';

                                } else {

                                    echo '<span class="status status-pending">'
                                         . htmlspecialchars($row['status']) .
                                         '</span>';

                                }

                                ?>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="view-details-btn"
                                    onclick="openComplaintModal(
                                        <?php echo htmlspecialchars(json_encode($row['subject'])); ?>,
                                        <?php echo htmlspecialchars(json_encode($row['category'])); ?>,
                                        <?php echo htmlspecialchars(json_encode(date('d M Y', strtotime($row['created_at'])))); ?>,
                                        <?php echo htmlspecialchars(json_encode($row['status'])); ?>,
                                        <?php echo htmlspecialchars(json_encode($row['description'])); ?>
                                    )"
                                >
                                    View
                                </button>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>


        <?php } else { ?>


            <!-- Empty State -->

            <div class="no-complaints-card">

                <div class="no-complaints-icon">
                    📋
                </div>

                <p>
                    You have not submitted any complaints yet.
                </p>

            </div>


        <?php } ?>


        <!-- Bottom Buttons -->

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


<!-- Complaint Details Modal -->

<div
    id="complaintModal"
    class="complaint-modal"
    onclick="closeComplaintModal(event)"
>

    <div
        class="complaint-modal-content"
        onclick="event.stopPropagation()"
    >

        <button
            type="button"
            class="close-modal"
            onclick="closeComplaintModal()"
        >
            &times;
        </button>

        <h2>
            Complaint Details
        </h2>


        <div class="detail-row">

            <strong>
                Subject
            </strong>

            <span id="modalSubject"></span>

        </div>


        <div class="detail-row">

            <strong>
                Category
            </strong>

            <span id="modalCategory"></span>

        </div>


        <div class="detail-row">

            <strong>
                Submitted Date
            </strong>

            <span id="modalDate"></span>

        </div>


        <div class="detail-row">

            <strong>
                Status
            </strong>

            <span id="modalStatus"></span>

        </div>


        <div class="detail-row">

            <strong>
                Complaint Description
            </strong>

            <div
                id="modalDescription"
                class="detail-description"
            ></div>

        </div>

    </div>

</div>


<script>

function openComplaintModal(
    subject,
    category,
    date,
    status,
    description
) {

    document.getElementById("modalSubject").textContent = subject;

    document.getElementById("modalCategory").textContent = category;

    document.getElementById("modalDate").textContent = date;

    document.getElementById("modalStatus").textContent = status;

    document.getElementById("modalDescription").textContent = description;

    document.getElementById("complaintModal").style.display = "flex";

}


function closeComplaintModal(event) {

    if (
        !event ||
        event.target.id === "complaintModal"
    ) {

        document.getElementById("complaintModal").style.display = "none";

    }

}

</script>


</body>

</html>