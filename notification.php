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

// Function to fetch new studies added within the past week and delete those not in the database
function getWeeklyNotifications($conn) {
    $notifications = [];

    // Query to find studies added within the last 7 days
    $query = "
        SELECT s.study_id, s.title, c.course
        FROM studytbl s
        JOIN categorytbl c ON s.study_id = c.study_id
        WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if the study is still in the database
            $checkQuery = "SELECT COUNT(*) as count FROM studytbl WHERE study_id = " . $row['study_id'];
            $checkResult = $conn->query($checkQuery);
            $checkRow = $checkResult->fetch_assoc();

            if ($checkRow['count'] == 0) {
                // Study not found in the database, delete it from notifications
                $deleteQuery = "DELETE FROM studytbl WHERE study_id = " . $row['study_id'];
                $conn->query($deleteQuery);
            } else {
                // Add valid study to notifications
                $notifications[] = [
                    'title' => $row['title'],
                    'course' => $row['course']
                ];
            }
        }
    }

    return $notifications;
}

// Fetch notifications
$notifications = getWeeklyNotifications($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Books</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #eeeeee;
            margin: 0;
        }
        
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: darkblue;
            color: white;
            font-weight: bold;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: black;
        }
        .sidebar .sidebar-brand {
            font-size: 24px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .sidebar .sidebar-brand img {
            border-radius: 50%;
        }

        /* Content style with left margin to prevent overlap */
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="imgs/logo.jpg" height="50" alt="Digi-Studies"> Digi - Studies
        </div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="./sections/IT.php"><i class="fas fa-laptop"></i> College of Computer Studies</a>
        <a href="./sections/BA.php"><i class="fas fa-briefcase"></i> Business Administration</a>
        <a href="./sections/TEP.php"><i class="fas fa-chalkboard-teacher"></i> Teachers Education Program</a>
        <a href="add_favorite.php"><i class="fas fa-star"></i> Favorites</a>
        <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
        <a href="help.php"><i class="fas fa-pencil"></i> Help</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h2>Notifications</h2>
        <ul class="list-group">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item">
                        <strong>New Study Added:</strong> <?php echo htmlspecialchars($notification['title']); ?>
                        <br>
                        <em>Course:</em> <?php echo htmlspecialchars($notification['course']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item">No new studies added this week.</li>
            <?php endif; ?>
        </ul>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>
