<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch groups where the user is a leader
$sql = "SELECT sg.GroupID, sg.GroupName FROM StudyGroups sg
        INNER JOIN GroupMembers gm ON sg.GroupID = gm.GroupID
        WHERE gm.UserID = ? AND gm.Role = 'Leader'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$groups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle form submission for adding a study session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST["group_id"];
    $session_date = $_POST["session_date"];
    $session_title = trim($_POST["session_title"]);
    $description = trim($_POST["description"]);
    $start_time = $session_date . ' ' . $_POST["start_time"];
    $end_time = $session_date . ' ' . $_POST["end_time"];

    // Validate input
    if (!empty($group_id) && !empty($session_title) && !empty($session_date) && !empty($start_time) && !empty($end_time)) {
        $sql = "INSERT INTO StudySessions (GroupID, SessionTitle, Description, SessionDate, StartTime, EndTime) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $group_id, $session_title, $description, $session_date, $start_time, $end_time);

        if ($stmt->execute()) {
            $success_message = "Study session added successfully!";
        } else {
            $error_message = "Failed to add the study session. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}

// Fetch user's sessions to display them
$sql = "SELECT * FROM StudySessions WHERE GroupID IN (SELECT GroupID FROM GroupMembers WHERE UserID = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sessions_result = $stmt->get_result();
$sessions = $sessions_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle deletion of a session
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM StudySessions WHERE SessionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $delete_message = "Study session deleted successfully!";
    } else {
        $delete_message = "Failed to delete the study session.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Study Sessions</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Manage Study Sessions</h2>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php } elseif (!empty($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <?php if (!empty($delete_message)) { ?>
            <div class="alert alert-info">
                <?php echo $delete_message; ?>
            </div>
        <?php } ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Session Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session['SessionTitle']); ?></td>
                        <td><?php echo htmlspecialchars($session['Description']); ?></td>
                        <td><?php echo htmlspecialchars($session['SessionDate']); ?></td>
                        <td><?php echo htmlspecialchars($session['StartTime']); ?></td>
                        <td><?php echo htmlspecialchars($session['EndTime']); ?></td>
                        <td>
                            <a href="edit_session.php?id=<?php echo $session['SessionID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete_id=<?php echo $session['SessionID']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this session?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
