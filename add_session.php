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
    $session_date = $_POST["session_date"]; // The session date from the form
    $session_title = trim($_POST["session_title"]);
    $description = trim($_POST["description"]);
    $start_time = $session_date . ' ' . $_POST["start_time"];
    $end_time = $session_date . ' ' . $_POST["end_time"];

    // Validate input
    if (!empty($group_id) && !empty($session_title) && !empty($session_date) && !empty($start_time) && !empty($end_time)) {
        // Insert statement now includes the SessionDate
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Study Session</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Add Study Session</h1>

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
                        <label for="group_id">Select Group</label>
                        <select class="form-control" id="group_id" name="group_id" required>
                            <option value="">-- Select Group --</option>
                            <?php foreach ($groups as $group) { ?>
                                <option value="<?php echo $group['GroupID']; ?>">
                                    <?php echo htmlspecialchars($group['GroupName']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="session_date">Select Session Date</label>
                        <input type="date" class="form-control" id="session_date" name="session_date" required>
                    </div>

                    <div class="form-group">
                        <label for="session_title">Session Title</label>
                        <input type="text" class="form-control" id="session_title" name="session_title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Session</button>
                    <a class="btn btn-outline-dark" href="manage_sessions.php">View Sessions</a>
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

