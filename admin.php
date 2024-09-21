<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not logged in as an admin, redirect to the login page
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "capstonedb"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding study
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_study'])) {
    $title = $_POST['studyTitle'];
    $author = $_POST['studyAuthor'];
    $year = $_POST['studyYear'];
    $abstract = $_POST['studyAbstract'];
    $description = $_POST['studyDescription'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO studiestbl (title, author, year, abstract, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $title, $author, $year, $abstract, $description);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Study added successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'admin.php';</script>";
    }

    // Close statement
    $stmt->close();
}

// Fetch and display user logs if the User Log's button was clicked
if (isset($_POST['show_logs'])) {
    $sql = "
        SELECT u.user_id, u.credentials, a.activity, a.date
        FROM usertbl u
        JOIN activitylog_tbl a ON u.user_id = a.user_id
        ORDER BY a.date DESC
    ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='table'>";
        echo "<thead><tr><th>User ID</th><th>Credentials</th><th>Activity</th><th>Date</th></tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['user_id']) . "</td><td>" . htmlspecialchars($row['credentials']) . "</td><td>" . htmlspecialchars($row['activity']) . "</td><td>" . htmlspecialchars($row['date']) . "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No logs found.";
    }

    exit(); // Exit to prevent additional HTML from being loaded
}

// Close connection
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
            background-color: rgb(255, 175, 88);
        }
    </style>
</head>
<body>
<!-- Navbar Code Here - Remains Unchanged -->
<nav class="navbar navbar-expand-lg custom-color">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="imgs/book.png" height="70" alt=""> DIGI - BOOKS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Console
          </a>
          <ul class="dropdown-menu">
            <li><button class="dropdown-item" onclick="showDataAnalytics()">Data Analytics</button></li>
            <li><button class="dropdown-item" onclick="showAddStudyModal()">Add Study</button></li>
            <li><button class="dropdown-item" onclick="showUserLogs()">User Log's</button></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
      </ul>
    </div>
  </div>
</nav>

<!-- Modal for Adding Study -->
<div class="modal fade" id="addStudyModal" tabindex="-1" aria-labelledby="addStudyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStudyModalLabel">Add New Study</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="admin.php" method="POST">
          <div class="mb-3">
            <label for="studyTitle" class="form-label">Title</label>
            <input type="text" class="form-control" id="studyTitle" name="studyTitle" required>
          </div>
          <div class="mb-3">
            <label for="studyAuthor" class="form-label">Author</label>
            <input type="text" class="form-control" id="studyAuthor" name="studyAuthor" required>
          </div>
          <div class="mb-3">
            <label for="studyYear" class="form-label">Year</label>
            <input type="number" class="form-control" id="studyYear" name="studyYear" required>
          </div>
          <div class="mb-3">
            <label for="studyAbstract" class="form-label">Abstract</label>
            <textarea class="form-control" id="studyAbstract" name="studyAbstract" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="studyDescription" class="form-label">Description</label>
            <textarea class="form-control" id="studyDescription" name="studyDescription" rows="3" required></textarea>
          </div>
          <button type="submit" name="submit_study" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for User Logs -->
<div class="modal fade" id="userLogsModal" tabindex="-1" aria-labelledby="userLogsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userLogsModalLabel">User Logs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="userLogsContent">
        <!-- User logs will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    function showAddStudyModal() {
        var myModal = new bootstrap.Modal(document.getElementById('addStudyModal'));
        myModal.show();
    }

    function showUserLogs() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "admin.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('userLogsContent').innerHTML = xhr.responseText; // Load only logs content
                var myModal = new bootstrap.Modal(document.getElementById('userLogsModal'));
                myModal.show();
            }
        };
        xhr.send("show_logs=1");
    }
</script>
</body>
</html>
