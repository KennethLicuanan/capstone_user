<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "capstonedb"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch overall study count
$overallSql = "SELECT COUNT(*) AS count FROM studiestbl";
$overallResult = $conn->query($overallSql);
$overallCount = $overallResult->fetch_assoc()['count'];

// Fetch count for each type
$typeCounts = [
    'BSBA' => 0,
    'TEP' => 0,
    'BSIT' => 0 // Add BSIT
];

$typeSql = "SELECT type, COUNT(*) AS count FROM studiestbl GROUP BY type";
$typeResult = $conn->query($typeSql);

while ($row = $typeResult->fetch_assoc()) {
    if (isset($typeCounts[$row['type']])) {
        $typeCounts[$row['type']] = $row['count'];
    }
}

// Fetch notifications
$notificationsSql = "SELECT message FROM notifications ORDER BY created_at DESC";
$notificationsResult = $conn->query($notificationsSql);
$notifications = [];

while ($row = $notificationsResult->fetch_assoc()) {
    $notifications[] = $row['message'];
}

// Check for new studies and create notification
$latestStudySql = "SELECT title, type FROM studiestbl ORDER BY studies_id DESC LIMIT 1";
$latestStudyResult = $conn->query($latestStudySql);

if ($latestStudyResult->num_rows > 0) {
    $latestStudy = $latestStudyResult->fetch_assoc();
    $title = $latestStudy['title'];
    $type = $latestStudy['type'];
    $notificationMessage = "New study added: \"$title\" from course \"$type\"";

    // Insert notification if it's not already in the notifications table
    if (!in_array($notificationMessage, $notifications)) {
        $insertNotificationSql = "INSERT INTO notifications (message, created_at) VALUES (?, NOW())";
        $stmt = $conn->prepare($insertNotificationSql);
        $stmt->bind_param("s", $notificationMessage);
        $stmt->execute();
        $stmt->close();

        // Add the new notification to the notifications array for display
        $notifications[] = $notificationMessage;

        // Check if notifications exceed 10
        if (count($notifications) > 10) {
            // Delete the oldest notification
            $deleteOldestNotificationSql = "DELETE FROM notifications ORDER BY created_at ASC LIMIT 1";
            $conn->query($deleteOldestNotificationSql);
        }
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Books</title>
    <link rel="stylesheet" href="dash.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #eeeeee;
        }
        .analytics-section {
            padding: 110px 10px;
        }
        .analytics-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .analytics-card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        .card-title i {
            margin-right: 10px;
            font-size: 24px;
        }
        .progress {
            height: 20px;
            border-radius: 10px;
            background-color: #e0e0e0;
            margin-top: 10px;
        }
        .progress-bar {
            border-radius: 10px;
        }
        .text-count {
            font-size: 14px;
            margin-top: 10px;
            text-align: right;
            color: #555;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-color">
      <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard.php"><img src="imgs/book.png" height="70" alt=""> DIGI - BOOKS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="courseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Courses
              </a>
              <ul class="dropdown-menu" aria-labelledby="courseDropdown">
                <li><a class="dropdown-item" href="./sections/IT.php">College of Computer Studies</a></li>
                <li><a class="dropdown-item" href="./sections/BA.php">Business Administration</a></li>
                <li><a class="dropdown-item" href="./sections/TEP.php">Teachers Education Program</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="favorites.php">Favorites</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="clearNotificationCount()">
                    Notifications <span class="badge bg-danger" id="notificationCount"><?php echo count($notifications); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                    <?php if (empty($notifications)): ?>
                        <li><a class="dropdown-item" href="#">No notifications</a></li>
                    <?php else: ?>
                        <?php foreach ($notifications as $notification): ?>
                            <li><a class="dropdown-item" href="#"><?php echo htmlspecialchars($notification); ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="help.php">Help</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <section class="analytics-section">
    <div class="container">
        <div class="row">
            <!-- Total Research Studies -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h5 class="card-title"><i class="fas fa-book"></i> TOTAL RESEARCH STUDIES</h5>
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-count"><?php echo $overallCount; ?> studies available</div>
                </div>
            </div>

            <!-- Total Studies of BSIT Program -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h5 class="card-title"><i class="fas fa-laptop-code"></i> COLLEGE OF COMPUTER STUDIES</h5>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($typeCounts['BSIT'] / $overallCount) * 100; ?>%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-count"><?php echo $typeCounts['BSIT']; ?> new studies</div>
                </div>
            </div>

            <!-- Total Studies of TEP Program -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h5 class="card-title"><i class="fas fa-graduation-cap"></i> TEACHERS EDUCATION PROGRAM</h5>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo ($typeCounts['TEP'] / $overallCount) * 100; ?>%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-count"><?php echo $typeCounts['TEP']; ?> new studies</div>
                </div>
            </div>

            <!-- Total Studies of BA Program -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h5 class="card-title"><i class="fas fa-briefcase"></i> BUSINESS ADMINISTRATION STUDIES</h5>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo ($typeCounts['BSBA'] / $overallCount) * 100; ?>%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-count"><?php echo $typeCounts['BSBA']; ?> new studies</div>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    function clearNotificationCount() {
        // Set notification count to 0
        document.getElementById('notificationCount').innerText = '0';

        // Optionally, you can also make an AJAX call to reset the count in the backend if needed.
        // For example:
        // fetch('reset_notification_count.php', { method: 'POST' });
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
