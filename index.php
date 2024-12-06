<?php
include('config.php');
session_start();

// Fetch all groups
$all_groups_sql = "SELECT * FROM studygroups LIMIT 6"; // Limit to 6 for display
$all_groups_result = $conn->query($all_groups_sql);

// Fetch total numbers for statistics
$total_users_sql = "SELECT COUNT(*) FROM users";
$total_users_result = $conn->query($total_users_sql);
$total_users = $total_users_result->fetch_row()[0];

$total_groups_sql = "SELECT COUNT(*) FROM studygroups";
$total_groups_result = $conn->query($total_groups_sql);
$total_groups = $total_groups_result->fetch_row()[0];

$total_sessions_sql = "SELECT COUNT(*) FROM studysessions";
$total_sessions_result = $conn->query($total_sessions_sql);
$total_sessions = $total_sessions_result->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Study Group Platform</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <style>
        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .jumbotron p {
            font-size: 1.5rem;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

    <div class="jumbotron text-center">
        <h1>Welcome to Virtual Study Group Platform</h1>
        <p>Collaborate, Learn, and Succeed Together</p>
        <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-5">Key Features</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-users feature-icon text-primary"></i>
                <h3>Group Management</h3>
                <p>Create and join study groups easily. Manage members and roles efficiently.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-calendar-alt feature-icon text-success"></i>
                <h3>Smart Scheduling</h3>
                <p>Schedule study sessions with automated reminders. Integrate with Google Calendar.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-book feature-icon text-info"></i>
                <h3>Resource Sharing</h3>
                <p>Upload and share study materials. Organize resources by topics or subjects.</p>
            </div>
        </div>
    </div>

    <div class="bg-light py-5 mt-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Community in Numbers</h2>
            <div class="row">
                <div class="col-md-4 text-center">
                    <h3 class="display-4"><?php echo $total_users; ?></h3>
                    <p class="lead">Active Users</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 class="display-4"><?php echo $total_groups; ?></h3>
                    <p class="lead">Study Groups</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 class="display-4"><?php echo $total_sessions; ?></h3>
                    <p class="lead">Study Sessions</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-5">Featured Study Groups</h2>
        <div class="row">
            <?php while ($group = $all_groups_result->fetch_assoc()) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($group['GroupName']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($group['Description']); ?></p>
                            <p class="card-text"><small class="text-muted">Created: <?php echo htmlspecialchars($group['CreatedAt']); ?></small></p>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-user-plus mr-2"></i>Join Group
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-outline-primary">View All Groups</a>
        </div>
    </div>

    <div class="bg-primary text-white py-5 mt-5">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Boost Your Study Experience?</h2>
            <a href="register.php" class="btn btn-light btn-lg">Join StudyBuddy Today</a>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>StudyBuddy</h5>
                    <p>Empowering students to learn together.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="mt-2">
                        <a href="#" class="text-white mr-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; 2024 Virtual Study Group Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>