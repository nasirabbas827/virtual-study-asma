<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// Fetch user data
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Handle Join Group request
if (isset($_POST['join_group'])) {
    $group_id = $_POST['group_id'];

    // Check if user is already a member
    $check_sql = "SELECT * FROM groupmembers WHERE GroupID = ? AND UserID = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $group_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Add user to the group
        $insert_sql = "INSERT INTO groupmembers (GroupID, UserID, Role) VALUES (?, ?, 'member')";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ii", $group_id, $user_id);
        if ($stmt_insert->execute()) {
            $join_message = "You have successfully joined the group!";
        } else {
            $join_message = "Failed to join the group. Please try again.";
        }
        $stmt_insert->close();
    } else {
        $join_message = "You are already a member of this group.";
    }
    $stmt->close();
}

// Fetch user's joined groups
$user_groups_sql = "SELECT sg.GroupID, sg.GroupName FROM studygroups sg
                    JOIN groupmembers gm ON sg.GroupID = gm.GroupID
                    WHERE gm.UserID = ?";
$stmt = $conn->prepare($user_groups_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_groups_result = $stmt->get_result();
$stmt->close();

// Fetch study sessions
$sessions_sql = "SELECT * FROM studysessions
                 WHERE GroupID IN (SELECT GroupID FROM groupmembers WHERE UserID = ?)
                 ORDER BY SessionDate, StartTime";
$stmt = $conn->prepare($sessions_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sessions_result = $stmt->get_result();
$stmt->close();

// Fetch all groups
$all_groups_sql = "SELECT * FROM studygroups";
$all_groups_result = $conn->query($all_groups_sql);

// Prepare sessions data for calendar
$calendar_events = array();
while ($session = $sessions_result->fetch_assoc()) {
    // Only use the SessionTitle and SessionDate (ignore StartTime and EndTime)
    $calendar_events[] = array(
        'title' => $session['SessionTitle'],
        'start' => $session['SessionDate'],  // Using SessionDate as the event date
        'description' => $session['Description']  // You can also display the description if needed
    );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>


    <div class="container mx-auto mt-8 px-4">
        <h2 class="text-3xl font-bold mb-6">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        
        <?php if (!empty($join_message)) { ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?php echo $join_message; ?></p>
            </div>
        <?php } ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- User's Joined Groups -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4"><i class="fas fa-users mr-2 text-blue-500"></i>Your Groups</h3>
                <ul class="space-y-2">
                    <?php while ($group = $user_groups_result->fetch_assoc()) { ?>
                        <li>
                            <a href="view_discussion.php?group_id=<?php echo $group['GroupID']; ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-chevron-right mr-2"></i><?php echo htmlspecialchars($group['GroupName']); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <!-- Calendar -->
            <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
                <h3 class="text-xl font-semibold mb-4"><i class="fas fa-calendar-alt mr-2 text-blue-500"></i>Study Session Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Study Sessions Notifications -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4"><i class="fas fa-bell mr-2 text-blue-500"></i>Upcoming Study Sessions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left">Title</th>
                            <th class="py-2 px-4 text-left">Description</th>
                            <th class="py-2 px-4 text-left">Date</th>
                            <th class="py-2 px-4 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sessions_result->data_seek(0);
                        while ($session = $sessions_result->fetch_assoc()) { 
                        ?>
                            <tr class="border-b">
                                <td class="py-2 px-4"><?php echo htmlspecialchars($session['SessionTitle']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($session['Description']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($session['SessionDate']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($session['StartTime']) . ' - ' . htmlspecialchars($session['EndTime']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Groups -->
        <div class="mt-8">
            <h3 class="text-2xl font-semibold mb-4"><i class="fas fa-globe mr-2 text-blue-500"></i>All Study Groups</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($group = $all_groups_result->fetch_assoc()) { ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($group['GroupName']); ?></h4>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($group['Description']); ?></p>
                        <p class="text-sm text-gray-500 mb-4">Created: <?php echo htmlspecialchars($group['CreatedAt']); ?></p>
                        <?php 
                        $user_groups_result->data_seek(0);
                        $is_member = false;
                        while ($user_group = $user_groups_result->fetch_assoc()) {
                            if ($user_group['GroupID'] == $group['GroupID']) {
                                $is_member = true;
                                break;
                            }
                        }
                        if ($is_member) { 
                        ?>
                            <a href="view_discussion.php?group_id=<?php echo $group['GroupID']; ?>" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300">
                                <i class="fas fa-comments mr-2"></i>View Discussion
                            </a>
                        <?php } else { ?>
                            <form method="POST">
                                <input type="hidden" name="group_id" value="<?php echo $group['GroupID']; ?>">
                                <button type="submit" name="join_group" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition duration-300">
                                    <i class="fas fa-user-plus mr-2"></i>Join Group
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo json_encode($calendar_events); ?>,
                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\nDescription: ' + info.event.extendedProps.description);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>