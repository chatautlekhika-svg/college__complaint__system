<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Help - College Complaint Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        /* ==========================================
           HELP PAGE
        ========================================== */

        .help-main {

            min-height: calc(100vh - 160px);

            padding: 50px 20px;

            background-color: #f8fafc;

        }


        .help-container {

            width: 100%;

            max-width: 1100px;

            margin: 0 auto;

        }


        /* ==========================================
           HEADING
        ========================================== */

        .help-heading {

            text-align: center;

            margin-bottom: 35px;

        }


        .help-heading h1 {

            margin: 0 0 10px;

            font-size: 36px;

            color: #123f6d;

        }


        .help-heading p {

            margin: 0;

            color: #666;

            font-size: 16px;

        }


        /* ==========================================
           HELP CARDS
        ========================================== */

        .help-card {

            background-color: #ffffff;

            border-radius: 12px;

            padding: 30px;

            margin-bottom: 25px;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);

        }


        .help-card h2 {

            margin-top: 0;

            margin-bottom: 15px;

            color: #123f6d;

            font-size: 23px;

        }


        .help-card p {

            color: #555;

            line-height: 1.8;

            font-size: 15px;

        }


        /* ==========================================
           STEPS
        ========================================== */

        .help-steps {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

            margin-top: 20px;

        }


        .help-step {

            background-color: #f8fafc;

            border-radius: 10px;

            padding: 22px;

        }


        .help-step-number {

            display: inline-flex;

            width: 38px;

            height: 38px;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background-color: #123f6d;

            color: white;

            font-weight: bold;

            margin-bottom: 12px;

        }


        .help-step h3 {

            margin: 0 0 8px;

            color: #123f6d;

            font-size: 18px;

        }


        .help-step p {

            margin: 0;

            font-size: 14px;

            line-height: 1.6;

        }


        /* ==========================================
           FAQ
        ========================================== */

        .faq-item {

            padding: 18px 0;

            border-bottom: 1px solid #e5e7eb;

        }


        .faq-item:last-child {

            border-bottom: none;

        }


        .faq-item h3 {

            margin: 0 0 8px;

            color: #123f6d;

            font-size: 17px;

        }


        .faq-item p {

            margin: 0;

            font-size: 14px;

            line-height: 1.7;

        }


        /* ==========================================
           LOGIN BUTTON
        ========================================== */

        .help-action {

            text-align: center;

            margin-top: 10px;

        }


        .help-login-btn {

            display: inline-block;

            padding: 12px 28px;

            background-color: #2f80b8;

            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-size: 16px;

            font-weight: 600;

        }


        .help-login-btn:hover {

            background-color: #256b99;

        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 750px) {

            .help-steps {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 600px) {

            .help-main {

                padding: 30px 15px;

            }


            .help-card {

                padding: 25px 20px;

            }


            .help-heading h1 {

                font-size: 30px;

            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     HEADER
========================================== -->

<header class="header">

    <div class="logo-section">

        <img
            src="assets/images/mbmc-logo.png"
            alt="Madan Bhandari Memorial College Logo"
            class="college-logo"
        >

        <div>

            <h2>
                Madan Bhandari Memorial College
            </h2>

            <p>
                College Complaint Management System
            </p>

        </div>

    </div>


    <nav class="navbar">

        <a href="index.php">
            Home
        </a>

        <a href="about.php">
            About
        </a>

        <a
            href="help.php"
            class="active"
        >
            Help
        </a>

        <a
            href="login.php"
            class="login-btn"
        >
            Login
        </a>

    </nav>

</header>



<!-- ==========================================
     MAIN CONTENT
========================================== -->

<main class="help-main">

    <div class="help-container">


        <!-- HEADING -->

        <div class="help-heading">

            <h1>
                Help & Support
            </h1>

            <p>
                Learn how to use the College Complaint Management System.
            </p>

        </div>



        <!-- HOW TO USE -->

        <div class="help-card">

            <h2>
                How to Use the System
            </h2>

            <div class="help-steps">


                <div class="help-step">

                    <div class="help-step-number">
                        1
                    </div>

                    <h3>
                        Login
                    </h3>

                    <p>
                        Login to the system using your registered
                        email and password.
                    </p>

                </div>


                <div class="help-step">

                    <div class="help-step-number">
                        2
                    </div>

                    <h3>
                        Submit a Complaint
                    </h3>

                    <p>
                        Open the Submit Complaint section and enter
                        your complaint subject, category and description.
                    </p>

                </div>


                <div class="help-step">

                    <div class="help-step-number">
                        3
                    </div>

                    <h3>
                        Track Your Complaint
                    </h3>

                    <p>
                        Open My Complaints to view your submitted
                        complaints and check their current status.
                    </p>

                </div>


                <div class="help-step">

                    <div class="help-step-number">
                        4
                    </div>

                    <h3>
                        Check the Status
                    </h3>

                    <p>
                        Your complaint may show Pending, In Progress
                        or Resolved depending on the administrator's update.
                    </p>

                </div>


            </div>

        </div>



        <!-- COMMON QUESTIONS -->

        <div class="help-card">

            <h2>
                Frequently Asked Questions
            </h2>


            <div class="faq-item">

                <h3>
                    Who can submit a complaint?
                </h3>

                <p>
                    Registered students and teachers can submit
                    complaints through the system.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    What information is required?
                </h3>

                <p>
                    You need to provide the complaint subject,
                    category and a description of the issue.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    How can I check my complaint status?
                </h3>

                <p>
                    Login to your account and open the My Complaints
                    section to view the status of your complaints.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    What do the complaint statuses mean?
                </h3>

                <p>
                    Pending means the complaint is waiting for review.
                    In Progress means the complaint is being handled.
                    Resolved means the complaint has been addressed.
                </p>

            </div>


            <div class="faq-item">

                <h3>
                    What should I do if I cannot log in?
                </h3>

                <p>
                    Check that your registered email and password
                    are entered correctly. You may also contact the
                    college administrator for assistance.
                </p>

            </div>

        </div>



        <!-- SUPPORT -->

        <div class="help-card">

            <h2>
                Need Help?
            </h2>

            <p>
                For assistance with your account or complaint,
                please contact the college administrator.
            </p>


            <div class="help-action">

                <a
                    href="login.php"
                    class="help-login-btn"
                >
                    Login to the System
                </a>

            </div>

        </div>


    </div>

</main>



<!-- ==========================================
     FOOTER
========================================== -->

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