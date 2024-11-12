<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
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

// Get the user ID from the session
$user_id = $_SESSION['user_id']; // Make sure this is set when the user logs in

// Handle removal from favorites
if (isset($_GET['remove']) && isset($_GET['study_id'])) {
    $study_id = $_GET['study_id'];
    $sql_remove = "DELETE FROM favoritestbl WHERE user_id = ? AND study_id = ?";
    $stmt_remove = $conn->prepare($sql_remove);
    $stmt_remove->bind_param("ii", $user_id, $study_id);
    if ($stmt_remove->execute()) {
        echo "<script>alert('Study removed from favorites.'); window.location.href = 'add_favorite.php';</script>";
    } else {
        echo "<script>alert('Failed to remove the study.');</script>";
    }
    $stmt_remove->close();
}

// Fetch favorite studies
$sql = "SELECT s.study_id, s.title, s.author, s.abstract, s.keywords, s.year, s.cNumber
        FROM favoritestbl f
        JOIN studytbl s ON f.study_id = s.study_id
        WHERE f.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// Initialize query with base SQL
$sql = "SELECT s.study_id, s.title, s.author, s.abstract, s.keywords, s.year, s.cNumber
        FROM favoritestbl f
        JOIN studytbl s ON f.study_id = s.study_id
        WHERE f.user_id = ?";

// Add filter conditions based on user input
$filters = [];
if (!empty($_GET['course'])) {
    $sql .= " AND c.course = ?";
    $filters[] = $_GET['course'];
}
if (!empty($_GET['year'])) {
    $sql .= " AND s.year = ?";
    $filters[] = $_GET['year'];
}
if (!empty($_GET['type'])) {
    $sql .= " AND c.type = ?";
    $filters[] = $_GET['type'];
}

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($filters) + 1), $user_id, ...$filters);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
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
            margin-top: 20px;
        }
        .study-card {
            background-color: white;
            margin-bottom: 15px;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .study-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .study-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
        }
        .study-card .card-header .study-title {
            font-size: 20px;
            color: #333;
        }
        .study-card .card-header .study-citation {
            font-size: 16px;
            color: #999;
            cursor: pointer;
        }
        .study-card .card-body {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }
        .study-card .card-body p {
            margin: 5px 0;
        }
        .study-card .remove-btn {
            border: none;
            background-color: transparent;
            color: #ff6f61;
            cursor: pointer;
        }
        .study-card .remove-btn:hover {
            text-decoration: underline;
        }
        .study-card .keywords {
            font-style: italic;
            color: #888;
        }
        .study-card .study-details {
            margin-top: 10px;
        }
        form .form-select, form .btn {
            min-width: 150px;
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

<div class="container">
<div class="container my-4">
    <form method="GET" class="row g-3">
        <!-- Course Filter -->
        <div class="col-md-4">
            <label for="course" class="form-label">Course</label>
            <select name="course" id="course" class="form-select">
                <option value="">All Courses</option>
                <!-- Add other course options here -->
                <option value="IT">IT</option>
                <option value="BA">BA</option>
                <option value="TEP">TEP</option>
            </select>
        </div>
        
        <!-- Year Filter -->
        <div class="col-md-4">
            <label for="year" class="form-label">Year</label>
            <select name="year" id="year" class="form-select">
                <option value="">All Years</option>
                <!-- Add year options here -->
                <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <!-- Type Filter -->
        <div class="col-md-4">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All Types</option>
                <!-- Add other type options here -->
                <option value="Thesis">Thesis</option>
                <option value="Capstone">Capstone</option>
            </select>
        </div>
        
        <!-- Submit Button -->
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
</div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="study-card">
                <div class="card-header">
                    <span class="study-title"><?php echo htmlspecialchars($row['title']); ?></span>
                    <span class="study-citation"><i class="fas fa-caret-up"></i> <?php echo htmlspecialchars($row['cNumber']); ?> <i class="fas fa-caret-down"></i></span>
                </div>
                <div class="card-body">
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                    <p><strong>Year:</strong> <?php echo htmlspecialchars($row['year']); ?></p>
                    <p><strong>Abstract:</strong> <?php echo htmlspecialchars($row['abstract']); ?></p>
                    <p><strong>Keywords:</strong> <span class="keywords"><?php echo htmlspecialchars($row['keywords']); ?></span></p>
                    <div class="study-details">
                        <a href="?remove=true&study_id=<?php echo $row['study_id']; ?>" class="btn btn-outline-warning btn-sm remove-btn">
                            <i class="fas fa-star"></i> Remove from Favorites
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No favorite studies found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
