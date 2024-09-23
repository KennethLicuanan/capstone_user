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

// Handle the "Add to Favorites" form submission
if (isset($_POST['add_favorite'])) {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id']; // Safe to use now

    // Check if the study is already in favorites
    $checkStmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND id = ?");
    $checkStmt->bind_param("ii", $user_id, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.getElementById("modalMessage").textContent = "This study is already in your favorites!";
                    var myModal = new bootstrap.Modal(document.getElementById("favoriteModal"));
                    myModal.show();
                });
              </script>';
    } else {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $id);

        if ($stmt->execute()) {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.getElementById("modalMessage").textContent = "Added to favorites successfully!";
                        var myModal = new bootstrap.Modal(document.getElementById("favoriteModal"));
                        myModal.show();
                    });
                  </script>';
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.getElementById("modalMessage").textContent = "Error adding to favorites. Please try again.";
                        var myModal = new bootstrap.Modal(document.getElementById("favoriteModal"));
                        myModal.show();
                    });
                  </script>';
        }

        $stmt->close();
    }

    $checkStmt->close();
}

// Variables for filtering
$identifier = isset($_POST['identifier']) ? $_POST['identifier'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';

// Fetch studies based on filters
$sql = "SELECT id, title, author, abstract, keywords, year FROM studiestbl WHERE type = 'BSBA'";

if ($identifier) {
    $sql .= " AND identifier = '$identifier'";
}

if ($year) {
    $sql .= " AND year = '$year'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Books</title>
    <link rel="stylesheet" href="sections.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
        }
        .study {
            margin-bottom: 30px;
        }
        .card {
            border: 1px solid black;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-weight: bold;
            font-size: 1.5rem;
            color: #333;
            text-transform: uppercase;
        }
        .card-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            font-style: italic;
        }
        .card-text {
            font-size: 1rem;
            line-height: 1.5;
        }
        .see-more {
            cursor: pointer;
            color: #007bff;
            font-weight: bold;
        }
        .filter-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .filter-section h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .btn {
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg custom-color">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php"><img src="imgs/book.png" height="70" alt=""> DIGI - BOOKS</a>
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
                    <a class="nav-link" href="../help.php">Help</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="filter-section">
    <div class="container">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="identifier" class="form-label">Filter by Identifier</label>
                    <select name="identifier" id="identifier" class="form-select">
                        <option value="">Select Identifier</option>
                        <option value="Operations Management" <?php if ($identifier === 'Operations Management') echo 'selected'; ?>>Operations Management</option>
                        <option value="Marketing Management" <?php if ($identifier === 'Marketing Management') echo 'selected'; ?>>Marketing Management</option>
                        <option value="Financial Management" <?php if ($identifier === 'Financial Management') echo 'selected'; ?>>Financial Management</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label">Filter by Year</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">Select Year</option>
                        <?php
                        // Fetch distinct years from the studiestbl
                        $yearSql = "SELECT DISTINCT year FROM studiestbl ORDER BY year DESC";
                        $yearResult = $conn->query($yearSql);
                        while ($yearRow = $yearResult->fetch_assoc()) {
                            echo '<option value="' . $yearRow['year'] . '"' . ($year == $yearRow['year'] ? ' selected' : '') . '>' . $yearRow['year'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="first">
    <div class="container">
        <h2>Business Administration Studies</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $abstract = htmlspecialchars($row['abstract']);
                    $shortAbstract = (strlen($abstract) > 100) ? substr($abstract, 0, 100) . '...' : $abstract;
                    $fullAbstract = $abstract;
                    ?>
                    <div class="col-md-4 study">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">by <?php echo htmlspecialchars($row['author']); ?></h6>
                                <p class="card-text">
                                    <span class="short-abstract"><?php echo $shortAbstract; ?></span>
                                    <span class="full-abstract d-none"><?php echo $fullAbstract; ?></span>
                                    <span class="see-more">See More</span>
                                </p>
                                <p class="card-text"><strong>Keywords:</strong> <?php echo htmlspecialchars($row['keywords']); ?></p>
                                <p class="card-text"><strong>Year:</strong> <?php echo htmlspecialchars($row['year']); ?></p>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="add_favorite" class="btn btn-primary">Add to Favorites</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No studies available for the selected criteria.</p>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="favoriteModal" tabindex="-1" aria-labelledby="favoriteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="favoriteModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMessage">
                <!-- Message will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.see-more').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var shortText = this.previousElementSibling.previousElementSibling;
            var fullText = this.previousElementSibling;

            if (fullText.classList.contains('d-none')) {
                shortText.classList.add('d-none');
                fullText.classList.remove('d-none');
                this.textContent = 'See Less';
            } else {
                shortText.classList.remove('d-none');
                fullText.classList.add('d-none');
                this.textContent = 'See More';
            }
        });
    });
</script>
</body>
</html>
