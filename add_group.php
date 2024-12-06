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

// Fetch the user data (username) from the database
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($username);
    $stmt->fetch();
} else {
    header("location: index.php");
    exit;
}
$stmt->close();

// Handle group creation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST["group_name"]);
    $description = trim($_POST["description"]);

    // Validate form input
    if (!empty($group_name)) {
        // Insert new group into StudyGroups table
        $sql = "INSERT INTO StudyGroups (GroupName, Description, CreatedBy) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $group_name, $description, $user_id);
        
        if ($stmt->execute()) {
            $group_id = $stmt->insert_id;
            $stmt->close();

            // Add creator as the leader in GroupMembers table
            $sql = "INSERT INTO GroupMembers (GroupID, UserID, Role) VALUES (?, ?, 'Leader')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $group_id, $user_id);

            if ($stmt->execute()) {
                $success_message = "Group created successfully!";
            } else {
                $error_message = "Failed to assign leader role. Please try again.";
            }
            $stmt->close();
        } else {
            $error_message = "Failed to create group. Please try again.";
        }
    } else {
        $error_message = "Group name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Group</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Create Study Groups</h1>

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
                        <label for="group_name">Group Name</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Enter group name" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter group description" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary ">Create Group</button>
                    <a class="btn btn-outline-dark" href="manage_groups.php">View Groups</a>
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
