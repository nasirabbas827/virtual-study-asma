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

// Get the group ID from the query parameter
if (isset($_GET['group_id']) && is_numeric($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    // Fetch group details
    $sql = "SELECT GroupName, Description FROM StudyGroups WHERE GroupID = ? AND CreatedBy = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $group = $result->fetch_assoc();
    } else {
        header("location: manage_groups.php");
        exit;
    }
    $stmt->close();
} else {
    header("location: manage_groups.php");
    exit;
}

// Handle form submission for updating group details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST["group_name"]);
    $description = trim($_POST["description"]);

    // Validate input
    if (!empty($group_name)) {
        $sql = "UPDATE StudyGroups SET GroupName = ?, Description = ? WHERE GroupID = ? AND CreatedBy = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $group_name, $description, $group_id, $user_id);

        if ($stmt->execute()) {
            $success_message = "Group updated successfully!";
        } else {
            $error_message = "Failed to update the group. Please try again.";
        }
        $stmt->close();

        // Refresh group details
        $group['GroupName'] = $group_name;
        $group['Description'] = $description;
    } else {
        $error_message = "Group name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Group</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Edit Group</h1>

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
                        <input type="text" class="form-control" id="group_name" name="group_name" value="<?php echo htmlspecialchars($group['GroupName']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($group['Description']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Group</button>
                    <a href="manage_groups.php" class="btn btn-outline-dark">Cancel</a>
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
