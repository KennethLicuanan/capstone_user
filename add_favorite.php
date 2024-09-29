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

// Handle filtering
$identifier = isset($_POST['identifier']) ? $_POST['identifier'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';

// Fetch favorite studies based on filters for the logged-in user
$sql = "SELECT s.id, s.title, s.author, s.abstract, s.keywords, s.year
        FROM favorites f
        JOIN studiestbl s ON f.id = s.id
        WHERE f.user_id = ?";

if ($identifier) {
    $sql .= " AND s.identifier = '$identifier'";
}

if ($year) {
    $sql .= " AND s.year = '$year'";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch notifications
$notificationsSql = "SELECT message FROM notifications ORDER BY created_at DESC";
$notificationsResult = $conn->query($notificationsSql);
$notifications = [];

while ($row = $notificationsResult->fetch_assoc()) {
    $notifications[] = $row['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dash.css">
    <style>
        body {
            background-color: #f0f0f0;
        }
        .study-card {
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .study-card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }
        .card-title {
            font-weight: bold;
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
    </style>
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
              <a class="nav-link" href="add_favorite.php">Favorites</a>
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
<!-- Filter Section -->
<section class="filter-section">
    <div class="container">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="identifier" class="form-label">Filter by Identifier</label>
                    <select name="identifier" id="identifier" class="form-select">
                        <option value="">Select Identifier</option>
                        <option value="WEB-BASED" <?php if ($identifier === 'WEB-BASED') echo 'selected'; ?>>WEB-BASED</option>
                        <option value="WEB-APP" <?php if ($identifier === 'WEB-APP') echo 'selected'; ?>>WEB-APP</option>
                        <option value="MOBILE APP" <?php if ($identifier === 'MOBILE APP') echo 'selected'; ?>>MOBILE APP</option>
                        <option value="IOT" <?php if ($identifier === 'IOT') echo 'selected'; ?>>IOT</option>
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

<!-- Favorite Studies Section -->
<div class="container mt-4">
    <h2>Your Favorite Studies</h2>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $abstract = htmlspecialchars($row['abstract']);
                $shortAbstract = (strlen($abstract) > 100) ? substr($abstract, 0, 100) . '...' : $abstract;
                ?>
                <div class="col-md-4">
                    <div class="card study-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">by <?php echo htmlspecialchars($row['author']); ?></h6>
                            <p class="card-text">
                                <span class="short-abstract"><?php echo $shortAbstract; ?></span>
                                <span class="see-more" data-abstract="<?php echo $abstract; ?>">See More</span>
                            </p>
                            <p class="card-text"><strong>Keywords:</strong> <?php echo htmlspecialchars($row['keywords']); ?></p>
                            <p class="card-text"><strong>Year:</strong> <?php echo htmlspecialchars($row['year']); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No favorite studies found.</p>";
        }
        ?>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="abstractModal" tabindex="-1" aria-labelledby="abstractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="abstractModalLabel">Full Abstract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalAbstractBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Open modal and set full abstract
        $('.see-more').on('click', function() {
            var fullAbstract = $(this).data('abstract');
            $('#modalAbstractBody').text(fullAbstract);
            $('#abstractModal').modal('show');
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // See More functionality
    $(document).ready(function() {
        $('.see-more').on('click', function() {
            var shortAbstract = $(this).siblings('.short-abstract');
            var fullAbstract = $(this).siblings('.full-abstract');

            if (shortAbstract.is(':visible')) {
                shortAbstract.hide();  // Hide short abstract
                fullAbstract.show();   // Show full abstract
                $(this).text('See Less'); // Change button text to "See Less"
            } else {
                fullAbstract.hide();    // Hide full abstract
                shortAbstract.show();   // Show short abstract
                $(this).text('See More'); // Change button text to "See More"
            }
        });

        // Initially hide all full abstracts
        $('.full-abstract').hide();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
