<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch groups where the user is a member
$sql = "SELECT sg.GroupID, sg.GroupName FROM StudyGroups sg
        INNER JOIN GroupMembers gm ON sg.GroupID = gm.GroupID
        WHERE gm.UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$groups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $group_id = $_POST['group_id'];
    $resource_title = trim($_POST['resource_title']);
    $topic = trim($_POST['topic']);
    
    // File upload validation
    $file = $_FILES['file'];
    $allowed_extensions = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if (in_array(strtolower($file_extension), $allowed_extensions)) {
        $target_dir = "uploads/";
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Move the file to the server directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Insert the resource into the database
            $sql = "INSERT INTO Resources (GroupID, UploadedBy, ResourceTitle, FilePath, Topic) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $group_id, $user_id, $resource_title, $target_file, $topic);

            if ($stmt->execute()) {
                $success_message = "Resource uploaded successfully!";
            } else {
                $error_message = "Failed to upload the resource.";
            }
            $stmt->close();
        } else {
            $error_message = "Failed to move the uploaded file.";
        }
    } else {
        $error_message = "Invalid file type. Allowed types: PDF, DOC, DOCX, PNG, JPG.";
    }
}

// Fetch the uploaded resources of the logged-in user
$sql = "SELECT r.ResourceID, r.ResourceTitle, r.FilePath, r.Topic, r.UploadedAt, sg.GroupName 
        FROM Resources r 
        INNER JOIN StudyGroups sg ON r.GroupID = sg.GroupID 
        WHERE r.UploadedBy = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resources_result = $stmt->get_result();
$resources = $resources_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle deletion of a resource
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch file path before deleting
    $sql = "SELECT FilePath FROM Resources WHERE ResourceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the resource record from the database
    $sql = "DELETE FROM Resources WHERE ResourceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        // Delete the file from the server
        unlink($file_path);
        $delete_message = "Resource deleted successfully!";
    } else {
        $delete_message = "Failed to delete the resource.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Study Resources</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Manage Study Resources</h1>

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

        <div class="card mx-auto mt-4" style="max-width: 600px;">
            <div class="card-body">
                <h3>Upload New Resource</h3>
                <form method="POST" enctype="multipart/form-data">
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
                        <label for="resource_title">Resource Title</label>
                        <input type="text" class="form-control" id="resource_title" name="resource_title" required>
                    </div>

                    <div class="form-group">
                        <label for="topic">Topic</label>
                        <input type="text" class="form-control" id="topic" name="topic">
                    </div>

                    <div class="form-group">
                        <label for="file">Select File</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload Resource</button>
                </form>
            </div>
        </div>

        <h3 class="mt-5">Your Uploaded Resources</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Resource Title</th>
                        <th>Topic</th>
                        <th>Uploaded At</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resource['ResourceTitle']); ?></td>
                            <td><?php echo htmlspecialchars($resource['Topic']); ?></td>
                            <td><?php echo htmlspecialchars($resource['UploadedAt']); ?></td>
                            <td><a href="<?php echo $resource['FilePath']; ?>" target="_blank" class="btn btn-info btn-sm">View</a></td>
                            <td>
                                <a href="?delete_id=<?php echo $resource['ResourceID']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this resource?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
