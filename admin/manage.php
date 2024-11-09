<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Display any session message
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
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

// Initialize search results to null to clear previous results
$searchResults = null;

// Fetch studies based on search input (course or type)
function fetchStudiesBySearch($conn, $searchTerm) {
    $query = "SELECT studytbl.study_id, studytbl.title, studytbl.author, studytbl.abstract, studytbl.keywords, studytbl.year, studytbl.cNumber, categorytbl.course, categorytbl.type 
              FROM studytbl 
              INNER JOIN categorytbl ON studytbl.study_id = categorytbl.study_id 
              WHERE categorytbl.course LIKE '%$searchTerm%' OR categorytbl.type LIKE '%$searchTerm%'";
    $result = $conn->query($query);
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    $searchResults = fetchStudiesBySearch($conn, $searchTerm);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Studies</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
            left: 0;
            background-color: darkblue;
            padding-top: 20px;
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
        .course-section input[type="text"] {
            margin-bottom: 15px;
            border-radius: 5px;
            padding: 10px;
        }
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
    <h2>Welcome to Digi-Studies</h2>

    <div class="row">
        <!-- Search Card -->
        <div class="col-md-6 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Search Studies</h5>
                    <p class="card-text">Find studies by course or type.</p>
                    <!-- Search Button to Trigger Modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Archived Studies Card -->
        <div class="col-md-6 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Archived Studies</h5>
                    <p class="card-text">Access archived studies.</p>
                    <!-- Navigate to Archive Page -->
                    <a href="archives.php" class="btn btn-secondary">Go to Archived Studies</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search Studies</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Form inside Modal -->
                    <form method="post">
                        <div class="form-group">
                            <input type="text" name="search_term" class="form-control" placeholder="Search Course or Type here">
                        </div>
                        <button type="submit" name="search" class="btn btn-primary mt-3">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (isset($searchResults) && $searchResults->num_rows > 0): ?>
        <div class="mt-4">
            <h5>Search Results</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Abstract</th>
                        <th>Keywords</th>
                        <th>Year</th>
                        <th>Call Number</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $searchResults->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['abstract']) ?></td>
                            <td><?= htmlspecialchars($row['keywords']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td><?= htmlspecialchars($row['cNumber']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td>
                                <form action="update_study.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="study_id" value="<?= $row['study_id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </form>
                                <form action="delete_study.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="study_id" value="<?= $row['study_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])): ?>
        <div class="alert alert-info mt-4">No results found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

<?php $conn->close(); ?>
