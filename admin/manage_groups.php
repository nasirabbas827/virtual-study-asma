<?php
// Include the database configuration file
include('config.php');

// Initialize error and success message variables
$delete_err = "";
$delete_success = "";

// Handle the deletion of a group and its members
if (isset($_GET['delete_group_id'])) {
    $group_id = $_GET['delete_group_id'];

    // Begin a transaction to delete the group and its associated members
    mysqli_begin_transaction($conn);

    try {
        // Delete all members of the group
        $delete_members_sql = "DELETE FROM groupmembers WHERE GroupID = ?";
        $stmt_members = mysqli_prepare($conn, $delete_members_sql);
        mysqli_stmt_bind_param($stmt_members, "i", $group_id);
        mysqli_stmt_execute($stmt_members);
        mysqli_stmt_close($stmt_members);

        // Delete the group itself
        $delete_group_sql = "DELETE FROM studygroups WHERE GroupID = ?";
        $stmt_group = mysqli_prepare($conn, $delete_group_sql);
        mysqli_stmt_bind_param($stmt_group, "i", $group_id);
        mysqli_stmt_execute($stmt_group);
        mysqli_stmt_close($stmt_group);

        // Commit the transaction
        mysqli_commit($conn);
        $delete_success = "Group and its members deleted successfully.";
    } catch (Exception $e) {
        // If any error occurs, roll back the transaction
        mysqli_roll_back($conn);
        $delete_err = "Failed to delete the group and its members.";
    }
}

// Fetch all groups with the corresponding usernames
$sql_groups = "SELECT g.*, u.username FROM studygroups g 
               JOIN users u ON g.CreatedBy = u.id";
$result_groups = mysqli_query($conn, $sql_groups);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Groups</title>

    <!-- Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Manage Study Groups</h2>

    <!-- Success or Error Message -->
    <?php if (!empty($delete_success)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $delete_success; ?>
        </div>
    <?php elseif (!empty($delete_err)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $delete_err; ?>
        </div>
    <?php endif; ?>

    <!-- Groups Section -->
    <h3>Study Groups</h3>
    <?php if (mysqli_num_rows($result_groups) == 0): ?>
        <div class="alert alert-warning" role="alert">
            No groups found.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>GroupID</th>
                    <th>GroupName</th>
                    <th>Description</th>
                    <th>CreatedBy (Username)</th>
                    <th>CreatedAt</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($group = mysqli_fetch_assoc($result_groups)): ?>
                    <tr>
                        <td><?php echo $group['GroupID']; ?></td>
                        <td><?php echo $group['GroupName']; ?></td>
                        <td><?php echo $group['Description']; ?></td>
                        <td><?php echo $group['username']; ?></td>
                        <td><?php echo $group['CreatedAt']; ?></td>
                        <td>
                            <a href="manage_groups.php?delete_group_id=<?php echo $group['GroupID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this group and all its members?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<!-- Bootstrap JS & dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
