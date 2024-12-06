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

// Fetch the session details if ID is provided
if (isset($_GET['id'])) {
    $session_id = $_GET['id'];

    // Fetch session data
    $sql = "SELECT * FROM StudySessions WHERE SessionID = ? AND GroupID IN (SELECT GroupID FROM GroupMembers WHERE UserID = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $session_id, $user_id);
    $stmt->execute();
    $session_result = $stmt->get_result();
    $session = $session_result->fetch_assoc();
    $stmt->close();

    // Check if session exists
    if (!$session) {
        header("Location: manage_sessions.php"); // Redirect if session doesn't exist
        exit;
    }
}

// Handle form submission for editing the session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session_title = trim($_POST["session_title"]);
    $description = trim($_POST["description"]);
    $session_date = $_POST["session_date"];
    $start_time = $session_date . ' ' . $_POST["start_time"];
    $end_time = $session_date . ' ' . $_POST["end_time"];

    if (!empty($session_title) && !empty($description) && !empty($start_time) && !empty($end_time)) {
        $sql = "UPDATE StudySessions SET SessionTitle = ?, Description = ?, SessionDate = ?, StartTime = ?, EndTime = ? WHERE SessionID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $session_title, $description, $session_date, $start_time, $end_time, $session_id);

        if ($stmt->execute()) {
            $success_message = "Study session updated successfully!";
        } else {
            $error_message = "Failed to update the study session.";
        }
        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Study Session</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Edit Study Session</h1>

        <div class="card mx-auto mt-4" style="max-width: 600px;">
            <div class="card-body">
                <?php if (!empty($success_message)) { ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php } elseif (!empty($error_message)) { ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="session_title">Session Title</label>
                        <input type="text" class="form-control" id="session_title" name="session_title" value="<?php echo htmlspecialchars($session['SessionTitle']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($session['Description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="session_date">Session Date</label>
                        <input type="date" class="form-control" id="session_date" name="session_date" value="<?php echo htmlspecialchars($session['SessionDate']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" value="<?php echo htmlspecialchars($session['StartTime']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" value="<?php echo htmlspecialchars($session['EndTime']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Session</button>
                    <a href="manage_sessions.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

