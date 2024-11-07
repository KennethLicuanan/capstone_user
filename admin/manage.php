<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Ensure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">User ID not found. Please log in again.</div>';
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

// Function to fetch studies by course
function fetchStudiesByCourse($conn, $course) {
    $query = "SELECT studytbl.study_id, studytbl.title, studytbl.author, studytbl.abstract, studytbl.keywords, studytbl.year, categorytbl.type 
              FROM studytbl 
              INNER JOIN categorytbl ON studytbl.study_id = categorytbl.study_id 
              WHERE categorytbl.course = '$course'";
    $result = $conn->query($query);
    return $result;
}

// Fetch studies for each course
$itStudies = fetchStudiesByCourse($conn, 'IT');
$baStudies = fetchStudiesByCourse($conn, 'BA');
$tepStudies = fetchStudiesByCourse($conn, 'TEP');

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $study_id = $_POST['study_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $abstract = $_POST['abstract'];
    $keywords = $_POST['keywords'];

    $sql = "UPDATE studytbl SET title = ?, author = ?, year = ?, abstract = ?, keywords = ? WHERE study_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $author, $year, $abstract, $keywords, $study_id);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Study updated successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error updating study: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $study_id = $_POST['study_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from categorytbl
        $sqlCategory = "DELETE FROM categorytbl WHERE study_id = ?";
        $stmtCategory = $conn->prepare($sqlCategory);
        $stmtCategory->bind_param("i", $study_id);
        $stmtCategory->execute();
        $stmtCategory->close();

        // Delete from studytbl
        $sqlStudy = "DELETE FROM studytbl WHERE study_id = ?";
        $stmtStudy = $conn->prepare($sqlStudy);
        $stmtStudy->bind_param("i", $study_id);
        $stmtStudy->execute();
        $stmtStudy->close();

        // Commit transaction
        $conn->commit();
        echo '<div class="alert alert-success">Study deleted successfully from both tables.</div>';

    } catch (Exception $e) {
        // Rollback transaction if any query fails
        $conn->rollback();
        echo '<div class="alert alert-danger">Error deleting study: ' . $e->getMessage() . '</div>';
    }
}


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
            background-color: #ffffff;
            margin-left: 250px; /* Leave space for the sidebar */
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            text-align: start;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            overflow-x: hidden;
            background-color: darkblue;
            font-weight: bold;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
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
            color: white;
            text-align: center;
        }
        .sidebar .sidebar-brand img {
            border-radius: 50%;
        }


        .content {
        padding: 20px;
        }
        .content h2 {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }
        
        /* Course Section Header */
        .course-section h3 {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 1.5rem;
        }
        
        /* Search Bar Styling */
        .course-section input[type="text"] {
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }
        
        /* Study Card Styling */
        .study-card {
            margin-bottom: 20px;
        }
        .study-card .card {
            border: 1px solid #e1e5ee;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .study-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Card Title and Author */
        .study-card .card-title {
            color: #007bff;
            font-weight: bold;
        }
        .study-card .card-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Abstract and Keywords Styling */
        .study-card .card-text {
            color: #4b4b4b;
        }
        .study-card .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .study-card .btn-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sidebar">
        <div class="sidebar-brand">
            <img src="imgs/logo.jpg" height="50" alt="Digi-Studies"> Digi - Studies
        </div>
        <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="IT.php"><i class="fas fa-laptop"></i> College of Computer Studies</a>
        <a href="BA.php"><i class="fas fa-briefcase"></i> Business Administration</a>
        <a href="TEP.php"><i class="fas fa-chalkboard-teacher"></i> Teachers Education Program</a>
        <a href="add.php"><i class="fas fa-plus"></i> Add Study</a>
        <a href="manage.php"><i class="fas fa-tasks"></i> Manage Studies</a>
        <a href="user.php"><i class="fas fa-users"></i> User Logs</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
    </div>

<script>

    function openEditModal(study_id, title, author, year, abstract, keywords) {
            document.getElementById('edit_study_id').value = study_id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_abstract').value = abstract;
            document.getElementById('edit_keywords').value = keywords;

            // Show the modal
            $('#editStudyModal').modal('show');
        }
    function filterStudies(course) {
        var input, filter, table, rows, cells, i, j, match;
        input = document.querySelector("input[placeholder='Search " + course + " Studies']");
        filter = input.value.toLowerCase();
        table = document.querySelector(".course-section." + course + " table");
        rows = table.getElementsByTagName("tr");

        for (i = 1; i < rows.length; i++) {
            rows[i].style.display = "none";
            cells = rows[i].getElementsByTagName("td");
            match = false;

            for (j = 0; j < cells.length - 1; j++) {
                if (cells[j].innerText.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }

            if (match) {
                rows[i].style.display = "";
            }
        }
    }
</script>

</body>
</html>
