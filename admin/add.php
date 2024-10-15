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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['studyFile'])) {
    $file = $_FILES['studyFile'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    // Get file details
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    $fileTmpPath = $file['tmp_name'];

    // Define the file path where the file will be stored
    $filePath = "uploads/" . basename($fileName);

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($fileTmpPath, $filePath)) {
        // Insert file details into the database
        $stmt = $conn->prepare("INSERT INTO filestbl (file_name, file_size, file_type, file_path, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("siss", $fileName, $fileSize, $fileType, $filePath);
        
        if ($stmt->execute()) {
            echo "New study uploaded successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error moving the uploaded file.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Studies</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-color">
      <div class="container-fluid">
        <a class="navbar-brand" href="../admin.php"><img src="imgs/book.png" height="70" alt=""> Digi - Studies Administrator</a>
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
                <li><a class="dropdown-item" href="IT.php">College of Computer Studies</a></li>
                <li><a class="dropdown-item" href="BA.php">Business Administration</a></li>
                <li><a class="dropdown-item" href="TEP.php">Teachers Education Program</a></li>
              </ul>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="add.php">Add Study</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="manage.php">Manage Studies</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php">User Log's</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
          
        </div>
      </div>
    </nav>

    <div class="container mt-5">
    <h2>Add New Study</h2>
    <form action="add_study.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="studyFile" class="form-label">Choose a Study File</label>
            <input type="file" class="form-control" id="studyFile" name="studyFile" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Study</button>
    </form>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
