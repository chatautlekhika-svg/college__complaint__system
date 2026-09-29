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

$message = "";
$message_type = "";

$subject = "";
$category = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $subject = trim($_POST['subject']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    if ($subject == "" || $category == "" || $description == "") {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } else {

        $user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO complaints
                (user_id, subject, category, description)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "isss",
            $user_id,
            $subject,
            $category,
            $description
        );

        if ($stmt->execute()) {

            $message = "Complaint submitted successfully!";
            $message_type = "success";

            $subject = "";
            $category = "";
            $description = "";

        } else {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Complaint - Teacher</title>

    <link rel="stylesheet" href="../assets/css/complaint.css">

</head>

<body>


<!-- HEADER -->

<header class="complaint-header">

    <div class="complaint-brand">

        <img
            src="../assets/images/mbmc-logo.png"
            alt="MBMC Logo"
            class="complaint-logo"
        >

        <div class="complaint-brand-text">

            <h2>MADAN BHANDARI MEMORIAL COLLEGE</h2>

            <p>College Complaint Management System</p>

        </div>

    </div>


    <nav class="complaint-nav">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="submit_complaint.php" class="active">
            📝 Submit Complaint
        </a>

        <a href="my_complaints.php">
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


<!-- MAIN -->

<main class="complaint-main">

    <div class="complaint-container">


        <!-- TITLE -->

        <div class="complaint-title">

            <div class="complaint-title-icon">
                📝
            </div>

            <div>

                <h1>
                    Submit Complaint
                </h1>

                <p>
                    Submit your complaint to the college administration.
                </p>

            </div>

        </div>


        <!-- FORM -->

        <form
            method="POST"
            class="complaint-form"
        >


            <?php if ($message != "") { ?>

                <div class="complaint-<?php echo $message_type; ?>">

                    <?php echo $message; ?>

                </div>

            <?php } ?>


            <!-- SUBJECT -->

            <div class="complaint-form-group">

                <label for="subject">

                    <span class="form-label-icon">
                        📄
                    </span>

                    Complaint Subject

                </label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    placeholder="Enter complaint subject"
                    value="<?php echo htmlspecialchars($subject); ?>"
                    required
                >

            </div>


            <!-- CATEGORY -->

            <div class="complaint-form-group">

                <label for="category">

                    <span class="form-label-icon">
                        📋
                    </span>

                    Complaint Category

                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <option value="Academic">
                        Academic
                    </option>

                    <option value="Infrastructure">
                        Infrastructure
                    </option>

                    <option value="Faculty">
                        Faculty
                    </option>

                    <option value="Examination">
                        Examination
                    </option>

                    <option value="Library">
                        Library
                    </option>

                    <option value="IT / Computer">
                        IT / Computer
                    </option>

                    <option value="Cleanliness">
                        Cleanliness
                    </option>
                    <option value="Canteen">Canteen</option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <!-- DESCRIPTION -->

            <div class="complaint-form-group">

                <label for="description">

                    <span class="form-label-icon">
                        📝
                    </span>

                    Complaint Description

                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe your complaint in detail..."
                    required
                ><?php echo htmlspecialchars($description); ?></textarea>

            </div>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="complaint-submit-btn"
            >

                📤 &nbsp; Submit Complaint

            </button>


            <!-- BACK -->

            <a
                href="dashboard.php"
                class="complaint-back"
            >
                ← &nbsp; Back to Dashboard
            </a>


        </form>

    </div>

</main>


</body>

</html>