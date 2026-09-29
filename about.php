<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        About - College Complaint Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        /* ==========================================
           ABOUT PAGE
        ========================================== */

        .about-main {

            min-height: calc(100vh - 160px);

            padding: 50px 20px;

            background-color: #f8fafc;

        }


        .about-container {

            width: 100%;

            max-width: 1100px;

            margin: 0 auto;

        }


        /* ==========================================
           PAGE HEADING
        ========================================== */

        .about-heading {

            text-align: center;

            margin-bottom: 35px;

        }


        .about-heading h1 {

            margin: 0 0 10px;

            font-size: 36px;

            color: #123f6d;

        }


        .about-heading p {

            margin: 0;

            color: #666;

            font-size: 16px;

        }


        /* ==========================================
           ABOUT CARD
        ========================================== */

        .about-card {

            background-color: #ffffff;

            border-radius: 12px;

            padding: 35px;

            margin-bottom: 25px;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.08);

        }


        .about-card h2 {

            margin-top: 0;

            margin-bottom: 15px;

            color: #123f6d;

            font-size: 24px;

        }


        .about-card p {

            color: #555;

            line-height: 1.8;

            font-size: 15px;

            margin-bottom: 15px;

        }


        /* ==========================================
           FEATURES
        ========================================== */

        .about-features {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-top: 25px;

        }


        .about-feature {

            padding: 25px 20px;

            background-color: #f8fafc;

            border-radius: 10px;

            text-align: center;

        }


        .about-feature-icon {

            font-size: 35px;

            margin-bottom: 10px;

        }


        .about-feature h3 {

            margin: 0 0 10px;

            color: #123f6d;

            font-size: 18px;

        }


        .about-feature p {

            margin: 0;

            font-size: 14px;

            line-height: 1.6;

        }


        /* ==========================================
           USERS SECTION
        ========================================== */

        .about-users {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

            margin-top: 20px;

        }


        .about-user-box {

            padding: 25px;

            border-radius: 10px;

            background-color: #f8fafc;

        }


        .about-user-box h3 {

            margin-top: 0;

            color: #123f6d;

        }


        .about-user-box p {

            margin-bottom: 0;

        }


        /* ==========================================
           LOGIN BUTTON
        ========================================== */

        .about-action {

            text-align: center;

            margin-top: 30px;

        }


        .about-login-btn {

            display: inline-block;

            padding: 12px 28px;

            background-color: #2f80b8;

            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-size: 16px;

            font-weight: 600;

        }


        .about-login-btn:hover {

            background-color: #256b99;

        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 800px) {

            .about-features {

                grid-template-columns: 1fr;

            }


            .about-users {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 600px) {

            .about-main {

                padding: 30px 15px;

            }


            .about-card {

                padding: 25px 20px;

            }


            .about-heading h1 {

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
            alt="MBMC Logo"
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

        <a
            href="about.php"
            class="active"
        >
            About
        </a>

        <a href="help.php">
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

<main class="about-main">

    <div class="about-container">


        <!-- PAGE HEADING -->

        <div class="about-heading">

            <h1>
                About the System
            </h1>

            <p>
                College Complaint Management System
            </p>

        </div>



        <!-- ABOUT SYSTEM -->

        <div class="about-card">

            <h2>
                About College Complaint Management System
            </h2>

            <p>
                The College Complaint Management System is a web-based
                system developed to provide an organized and convenient
                way for students and teachers to submit complaints
                related to the college.
            </p>

            <p>
                The system allows complaints to be submitted online,
                stored securely, reviewed by the administrator, and
                tracked according to their status.
            </p>

            <p>
                Its main purpose is to make the complaint management
                process more systematic, transparent, and easier to
                manage.
            </p>

        </div>



        <!-- MAIN FEATURES -->

        <div class="about-card">

            <h2>
                Main Features
            </h2>


            <div class="about-features">


                <div class="about-feature">

                    <div class="about-feature-icon">
                        📝
                    </div>

                    <h3>
                        Submit Complaints
                    </h3>

                    <p>
                        Students and teachers can submit complaints
                        through the online system.
                    </p>

                </div>


                <div class="about-feature">

                    <div class="about-feature-icon">
                        📋
                    </div>

                    <h3>
                        Track Complaints
                    </h3>

                    <p>
                        Users can view their submitted complaints
                        and check their current status.
                    </p>

                </div>


                <div class="about-feature">

                    <div class="about-feature-icon">
                        👨‍💼
                    </div>

                    <h3>
                        Admin Management
                    </h3>

                    <p>
                        Administrators can review complaints and
                        update their status.
                    </p>

                </div>


            </div>

        </div>



        <!-- SYSTEM USERS -->

        <div class="about-card">

            <h2>
                Who Can Use the System?
            </h2>


            <div class="about-users">


                <div class="about-user-box">

                    <h3>
                        🎓 Students
                    </h3>

                    <p>
                        Students can register, log in, submit complaints,
                        and track the status of their complaints.
                    </p>

                </div>


                <div class="about-user-box">

                    <h3>
                        👨‍🏫 Teachers
                    </h3>

                    <p>
                        Teachers can register, log in, submit complaints,
                        and track the status of their complaints.
                    </p>

                </div>


            </div>

        </div>



        <!-- ADMIN -->

        <div class="about-card">

            <h2>
                Administrator
            </h2>

            <p>
                The administrator manages the complaint system.
                The administrator can view complaints from students
                and teachers, search and filter complaints, update
                complaint status, view registered users, and view
                complaint reports.
            </p>


            <div class="about-action">

                <a
                    href="login.php"
                    class="about-login-btn"
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