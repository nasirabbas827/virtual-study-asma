<?php
// Include the database configuration file
include('config.php');

// Initialize error and success message variables
$delete_err = "";
$delete_success = "";

// Handle the deletion of a resource
if (isset($_GET['delete_resource_id'])) {
    $resource_id = $_GET['delete_resource_id'];
    
    // SQL query to delete the resource
    $sql = "DELETE FROM resources WHERE ResourceID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $resource_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $delete_success = "Resource deleted successfully.";
    } else {
        $delete_err = "Failed to delete the resource.";
    }
    
    mysqli_stmt_close($stmt);
}

// Handle the deletion of a discussion post
if (isset($_GET['delete_post_id'])) {
    $post_id = $_GET['delete_post_id'];
    
    // SQL query to delete the discussion post
    $sql = "DELETE FROM discussionboard WHERE PostID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $delete_success = "Discussion post deleted successfully.";
    } else {
        $delete_err = "Failed to delete the discussion post.";
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch all resources with the corresponding usernames
$sql_resources = "SELECT r.*, u.username FROM resources r 
                  JOIN users u ON r.UploadedBy = u.id";
$result_resources = mysqli_query($conn, $sql_resources);

// Fetch all discussion posts with the corresponding usernames
$sql_discussions = "SELECT d.*, u.username FROM discussionboard d 
                    JOIN users u ON d.PostedBy = u.id";
$result_discussions = mysqli_query($conn, $sql_discussions);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resources and Discussions</title>

    <!-- Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Manage Resources and Discussions</h2>

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

    <!-- Resources Section -->
    <h3>Resources</h3>
    <?php if (mysqli_num_rows($result_resources) == 0): ?>
        <div class="alert alert-warning" role="alert">
            No resources found.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ResourceID</th>
                    <th>GroupID</th>
                    <th>UploadedBy (Username)</th>
                    <th>ResourceTitle</th>
                    <th>File</th>
                    <th>Topic</th>
                    <th>UploadedAt</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($resource = mysqli_fetch_assoc($result_resources)): ?>
                    <tr>
                        <td><?php echo $resource['ResourceID']; ?></td>
                        <td><?php echo $resource['GroupID']; ?></td>
                        <td><?php echo $resource['username']; ?></td>
                        <td><?php echo $resource['ResourceTitle']; ?></td>
                        <td>
                            <a href="../<?php echo $resource['FilePath']; ?>" target="_blank" class="btn btn-info">View File</a>
                        </td>
                        <td><?php echo $resource['Topic']; ?></td>
                        <td><?php echo $resource['UploadedAt']; ?></td>
                        <td>
                            <a href="manage_resources_and_discussions.php?delete_resource_id=<?php echo $resource['ResourceID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this resource?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Discussion Section -->
    <h3 class="mt-5">Discussions</h3>
    <?php if (mysqli_num_rows($result_discussions) == 0): ?>
        <div class="alert alert-warning" role="alert">
            No discussion posts found.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>PostID</th>
                    <th>GroupID</th>
                    <th>PostedBy (Username)</th>
                    <th>Content</th>
                    <th>PostedAt</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($discussion = mysqli_fetch_assoc($result_discussions)): ?>
                    <tr>
                        <td><?php echo $discussion['PostID']; ?></td>
                        <td><?php echo $discussion['GroupID']; ?></td>
                        <td><?php echo $discussion['username']; ?></td>
                        <td><?php echo $discussion['Content']; ?></td>
                        <td><?php echo $discussion['PostedAt']; ?></td>
                        <td>
                            <a href="manage_resources_and_discussions.php?delete_post_id=<?php echo $discussion['PostID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
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
