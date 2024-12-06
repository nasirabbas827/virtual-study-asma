<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for total users, study groups, resources, and sessions
$total_users_sql = "SELECT COUNT(*) FROM users";
$total_users_result = $conn->query($total_users_sql);
$total_users = $total_users_result->fetch_row()[0];

$total_groups_sql = "SELECT COUNT(*) FROM studygroups";
$total_groups_result = $conn->query($total_groups_sql);
$total_groups = $total_groups_result->fetch_row()[0];

$total_resources_sql = "SELECT COUNT(*) FROM resources";
$total_resources_result = $conn->query($total_resources_sql);
$total_resources = $total_resources_result->fetch_row()[0];

$total_sessions_sql = "SELECT COUNT(*) FROM studysessions";
$total_sessions_result = $conn->query($total_sessions_sql);
$total_sessions = $total_sessions_result->fetch_row()[0];

// Fetch study sessions for calendar
$sessions_sql = "SELECT SessionTitle, SessionDate, StartTime, EndTime, Description FROM studysessions ORDER BY SessionDate";
$sessions_result = $conn->query($sessions_sql);

$calendar_events = array();
while ($session = $sessions_result->fetch_assoc()) {
    $calendar_events[] = array(
        'title' => $session['SessionTitle'],
        'start' => $session['SessionDate'],
        'description' => $session['Description']
    );
}

// Fetch recent activities
$recent_activities_sql = "
    (SELECT 'New User' as type, username as name, created_at as created_at FROM users ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT 'New Resource' as type, ResourceTitle as name, UploadedAt as created_at FROM resources ORDER BY UploadedAt DESC LIMIT 5)
    UNION ALL
    (SELECT 'New Discussion' as type, SUBSTRING(Content, 1, 30) as name, PostedAt as created_at FROM discussionboard ORDER BY PostedAt DESC LIMIT 5)
    UNION ALL
    (SELECT 'New Group' as type, GroupName as name, CreatedAt as created_at FROM studygroups ORDER BY CreatedAt DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 10
";
$recent_activities_result = $conn->query($recent_activities_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudyBuddy</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-100">

<?php include('admin_navbar.php'); ?>

    <div class="container mx-auto mt-8 px-4">
        <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-semibold text-gray-800"><?php echo $total_users; ?></h2>
                        <p class="text-gray-600">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                        <i class="fas fa-user-friends fa-2x text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-semibold text-gray-800"><?php echo $total_groups; ?></h2>
                        <p class="text-gray-600">Study Groups</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                        <i class="fas fa-book fa-2x text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-semibold text-gray-800"><?php echo $total_resources; ?></h2>
                        <p class="text-gray-600">Resources</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                        <i class="fas fa-calendar-alt fa-2x text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-semibold text-gray-800"><?php echo $total_sessions; ?></h2>
                        <p class="text-gray-600">Study Sessions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4"><i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>Study Session Calendar</h2>
                    <div id="calendar" class="h-96"></div>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4"><i class="fas fa-history mr-2 text-indigo-500"></i>Recent Activities</h2>
                    <ul class="space-y-4">
                        <?php while ($activity = $recent_activities_result->fetch_assoc()) { ?>
                            <li class="flex items-center">
                                <?php
                                switch ($activity['type']) {
                                    case 'New User':
                                        echo '<i class="fas fa-user-plus text-blue-500 mr-3"></i>';
                                        break;
                                    case 'New Resource':
                                        echo '<i class="fas fa-file-upload text-yellow-500 mr-3"></i>';
                                        break;
                                    case 'New Discussion':
                                        echo '<i class="fas fa-comments text-green-500 mr-3"></i>';
                                        break;
                                    case 'New Group':
                                        echo '<i class="fas fa-users text-red-500 mr-3"></i>';
                                        break;
                                }
                                ?>
                                <div>
                                    <p class="font-semibold"><?php echo htmlspecialchars($activity['name']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo $activity['type']; ?> - <?php echo date('M d, Y', strtotime($activity['created_at'])); ?></p>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
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