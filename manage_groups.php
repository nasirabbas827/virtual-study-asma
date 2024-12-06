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

// Fetch user's groups
$sql = "SELECT GroupID, GroupName, Description FROM StudyGroups WHERE CreatedBy = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_groups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle group deletion
if (isset($_GET['delete_group_id'])) {
    $group_id = $_GET['delete_group_id'];

    // Delete group members first to maintain foreign key integrity
    $delete_members_sql = "DELETE FROM GroupMembers WHERE GroupID = ?";
    $stmt = $conn->prepare($delete_members_sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->close();

    // Delete the group
    $delete_group_sql = "DELETE FROM StudyGroups WHERE GroupID = ? AND CreatedBy = ?";
    $stmt = $conn->prepare($delete_group_sql);
    $stmt->bind_param("ii", $group_id, $user_id);
    if ($stmt->execute()) {
        $success_message = "Group deleted successfully!";
    } else {
        $error_message = "Failed to delete the group. Please try again.";
    }
    $stmt->close();

    // Refresh the page to reflect changes
    header("Location: manage_groups.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Groups</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <script>
        function confirmDelete(groupId) {
            if (confirm("Are you sure you want to delete this group? This action cannot be undone.")) {
                window.location.href = `manage_groups.php?delete_group_id=${groupId}`;
            }
        }
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Your Groups</h2>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php } elseif (!empty($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <table class="table table-bordered mt-4">
            <a class="btn btn-outline-dark float-right mb-3" href="view_members.php">View Members</a>
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($user_groups) > 0) { 
                    foreach ($user_groups as $group) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($group['GroupName']); ?></td>
                            <td><?php echo htmlspecialchars($group['Description']); ?></td>
                            <td>
                                <a href="edit_group.php?group_id=<?php echo $group['GroupID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $group['GroupID']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="3" class="text-center">No groups found. Create one!</td>
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
