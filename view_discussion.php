<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];
$group_id = $_GET['group_id'] ?? null;

// Validate group ID
if (empty($group_id)) {
    die("Invalid Group ID.");
}

// Fetch group details
$sql = "SELECT GroupName, Description FROM studygroups WHERE GroupID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $description);
$stmt->fetch();
$stmt->close();

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_post'])) {
    $content = $_POST['content'];

    if (!empty(trim($content))) {
        $insert_sql = "INSERT INTO DiscussionBoard (GroupID, PostedBy, Content) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iis", $group_id, $user_id, $content);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
}

// Fetch discussion posts
$posts_sql = "SELECT db.Content, db.PostedAt, u.username 
              FROM DiscussionBoard db 
              JOIN users u ON db.PostedBy = u.id 
              WHERE db.GroupID = ? 
              ORDER BY db.PostedAt DESC";
$stmt = $conn->prepare($posts_sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$posts_result = $stmt->get_result();

// Fetch resources
$resources_sql = "SELECT r.ResourceTitle, r.FilePath, r.Topic, r.UploadedAt, u.username 
                  FROM Resources r 
                  JOIN users u ON r.UploadedBy = u.id 
                  WHERE r.GroupID = ? 
                  ORDER BY r.UploadedAt DESC";
$stmt_resources = $conn->prepare($resources_sql);
$stmt_resources->bind_param("i", $group_id);
$stmt_resources->execute();
$resources_result = $stmt_resources->get_result();
$stmt_resources->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Group Discussion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .chat-section, .resources-section {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .chat-message {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chat-message strong {
            color: #007bff;
        }
        .resources-table {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .resources-table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4"><?php echo htmlspecialchars($group_name); ?> Discussion</h2>
        <p class="text-center mb-5"><?php echo htmlspecialchars($description); ?></p>

        <div class="row">
            <!-- Chat Section -->
            <div class="col-lg-6">
                <div class="chat-section">
                    <h3 class="section-title">Discussion Board</h3>
                    <div class="chat-box mb-3">
                        <?php while ($post = $posts_result->fetch_assoc()) { ?>
                            <div class="chat-message">
                                <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                <p class="mb-1"><?php echo nl2br(htmlspecialchars($post['Content'])); ?></p>
                                <small class="text-muted"><?php echo $post['PostedAt']; ?></small>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- New Post Form -->
                    <form method="POST" class="mt-3">
                        <div class="form-group">
                            <label for="content">Write a message:</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="new_post" class="btn btn-primary btn-block">Post</button>
                    </form>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="col-lg-6">
                <div class="resources-section">
                    <h3 class="section-title">Resources</h3>
                    <!-- Search Form -->
                    <form method="GET" class="mb-3">
                        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search resources">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    <!-- Resources Table -->
                    <div class="table-responsive resources-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Topic</th>
                                    <th>Uploaded By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($resource = $resources_result->fetch_assoc()) { 
                                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                                        $search_term = strtolower($_GET['search']);
                                        if (strpos(strtolower($resource['ResourceTitle']), $search_term) === false && 
                                            strpos(strtolower($resource['Topic']), $search_term) === false) {
                                            continue;
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($resource['ResourceTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($resource['Topic']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($resource['username']); ?>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($resource['UploadedAt']); ?></small>
                                        </td>
                                        <td>
                                            <a href="<?php echo htmlspecialchars($resource['FilePath']); ?>" class="btn btn-outline-primary btn-sm" download>Download</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>