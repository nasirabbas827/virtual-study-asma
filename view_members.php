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
$sql = "SELECT GroupID, GroupName FROM StudyGroups WHERE CreatedBy = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_groups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle member role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_role"])) {
    $group_id = $_POST["group_id"];
    $member_id = $_POST["member_id"];
    $new_role = $_POST["role"];

    $update_sql = "UPDATE GroupMembers SET Role = ? WHERE MemberID = ? AND GroupID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_role, $member_id, $group_id);

    if ($stmt->execute()) {
        $success_message = "Member role updated successfully!";
    } else {
        $error_message = "Failed to update member role. Please try again.";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Groups and Members</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <script>
        function toggleMembers(groupId) {
            const memberList = document.getElementById(`members-${groupId}`);
            memberList.style.display = memberList.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Your Groups and Members</h2>

        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php } elseif (!empty($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <?php if (count($user_groups) > 0) { 
            foreach ($user_groups as $group) { ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($group['GroupName']); ?></span>
                        <button class="btn btn-info btn-sm" onclick="toggleMembers(<?php echo $group['GroupID']; ?>)">View Members</button>
                    </div>
                    <div class="card-body" id="members-<?php echo $group['GroupID']; ?>" style="display: none;">
                        <?php
                        $members_sql = "SELECT GM.MemberID, U.username, GM.Role FROM GroupMembers GM JOIN Users U ON GM.UserID = U.id WHERE GM.GroupID = ?";
                        $stmt = $conn->prepare($members_sql);
                        $stmt->bind_param("i", $group['GroupID']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $members = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        if (count($members) > 0) { ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                                            <td><?php echo htmlspecialchars($member['Role']); ?></td>
                                            <td>
                                                <form method="POST" class="form-inline">
                                                    <input type="hidden" name="group_id" value="<?php echo $group['GroupID']; ?>">
                                                    <input type="hidden" name="member_id" value="<?php echo $member['MemberID']; ?>">
                                                    <select name="role" class="form-control mr-2">
                                                        <option value="Leader" <?php echo $member['Role'] == 'Leader' ? 'selected' : ''; ?>>Leader</option>
                                                        <option value="Member" <?php echo $member['Role'] == 'Member' ? 'selected' : ''; ?>>Member</option>
                                                    </select>
                                                    <button type="submit" name="update_role" class="btn btn-primary btn-sm">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p>No members found for this group.</p>
                        <?php } ?>
                    </div>
                </div>
            <?php } 
        } else { ?>
            <p>No groups found. Create one!</p>
        <?php } ?>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
