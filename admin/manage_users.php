<?php
// Include the database configuration file
include('config.php');

// Initialize error message variables
$delete_err = "";

// Handle the deletion of a user
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];
    
    // SQL query to delete a user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Successfully deleted user
        header("Location: manage_users.php?success=1"); // Redirect with success message
    } else {
        // Failed to delete user
        $delete_err = "Failed to delete the user.";
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch all users from the database
$sql = "SELECT id, username, email, phone, age, full_name, bio, created_at FROM users";
$result = mysqli_query($conn, $sql);

// Check if there are any users
if (mysqli_num_rows($result) == 0) {
    $users_list_msg = "No users found in the system.";
} else {
    $users_list_msg = "";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>

    <!-- Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Manage Users</h2>

    <!-- Success or Error Message -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success" role="alert">
            User deleted successfully.
        </div>
    <?php elseif (!empty($delete_err)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $delete_err; ?>
        </div>
    <?php endif; ?>

    <!-- Message when no users are found -->
    <?php if ($users_list_msg): ?>
        <div class="alert alert-warning" role="alert">
            <?php echo $users_list_msg; ?>
        </div>
    <?php else: ?>
        <!-- Table to display users -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Full Name</th>
                    <th>Bio</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td><?php echo $user['age']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['bio']; ?></td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td>
                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
