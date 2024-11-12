<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Ensure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">User ID not found. Please log in again.</div>';
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstonedb";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query if provided
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to get all IT studies
$sql = "SELECT s.study_id, s.title, s.author, s.abstract, s.keywords, s.year, s.cNumber
        FROM studytbl AS s
        JOIN categorytbl AS c ON s.study_id = c.study_id
        WHERE c.course = 'BA'";


// Add search conditions if there is a search query
if ($search) {
    $sql .= " AND (s.title LIKE '%$search%' OR s.author LIKE '%$search%' OR s.keywords LIKE '%$search%')";
}


// Fetch filter values from the URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Build the base query to fetch studies for the BA course
$query = "SELECT studytbl.*, categorytbl.type, categorytbl.course 
          FROM studytbl 
          LEFT JOIN categorytbl ON studytbl.study_id = categorytbl.study_id 
          WHERE categorytbl.course = 'BA'";

// Apply search, year, and type filters if provided
if (!empty($search)) {
    $query .= " AND (studytbl.title LIKE '%$search%' OR studytbl.author LIKE '%$search%' OR studytbl.keywords LIKE '%$search%')";
}
if (!empty($year)) {
    $query .= " AND studytbl.year = '$year'";
}
if (!empty($type)) {
    $query .= " AND categorytbl.type = '$type'";
}


$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Books - BA Studies</title>
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
            margin-top: 20px;
        }
        .study-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            font-family: Arial, sans-serif;
        }
        .study-title {
            font-size: 18px;
            color: #0066cc;
            cursor: pointer;
            text-decoration: underline;
        }
        .study-author-year {
            color: #555;
            font-size: 14px;
        }
        .study-abstract {
            margin-top: 5px;
            color: #333;
        }
        .cite-button {
            font-size: 14px;
            color: #0066cc;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <img src="imgs/logo.jpg" height="50" alt="Digi-Studies"> Digi - Studies
    </div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="IT.php"><i class="fas fa-laptop"></i> College of Computer Studies</a>
    <a href="BA.php"><i class="fas fa-briefcase"></i> Business Administration</a>
    <a href="TEP.php"><i class="fas fa-chalkboard-teacher"></i> Teachers Education Program</a>
    <a href="../add_favorite.php"><i class="fas fa-star"></i> Favorites</a>
    <a href="../notification.php"><i class="fas fa-bell"></i> Notifications</a>
    <a href="../help.php"><i class="fas fa-pencil"></i> Help</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">
    <!-- Search Form -->
    <form action="" method="get" class="mb-3 d-flex align-items-center" id="searchForm">
    <!-- Search Input -->
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search">
    
    <!-- Search Icon -->
    <span class="ms-2" style="cursor: pointer;" onclick="document.getElementById('searchForm').submit();">
        <i class="fas fa-search"></i>
    </span>

    <!-- Year Dropdown with Burger Icon -->
    <div class="btn-group ms-3">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <select name="year" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Year</option>
                    <?php
                    $yearQuery = "SELECT DISTINCT year FROM studytbl ORDER BY year DESC";
                    $yearResult = $conn->query($yearQuery);
                    while ($yearRow = $yearResult->fetch_assoc()) {
                        $selected = ($year == $yearRow['year']) ? 'selected' : '';
                        echo "<option value='{$yearRow['year']}' $selected>{$yearRow['year']}</option>";
                    }
                    ?>
                </select>
            </li>
        </ul>
    </div>

    <!-- Type Dropdown with Burger Icon -->
    <div class="btn-group ms-3">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <select name="type" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Type</option>
                    <?php
                    $typeQuery = "SELECT DISTINCT type FROM categorytbl WHERE course = 'IT' ORDER BY type";
                    $typeResult = $conn->query($typeQuery);
                    while ($typeRow = $typeResult->fetch_assoc()) {
                        $selected = ($type == $typeRow['type']) ? 'selected' : '';
                        echo "<option value='{$typeRow['type']}' $selected>{$typeRow['type']}</option>";
                    }
                    ?>
                </select>
            </li>
        </ul>
    </div>
</form>


    <!-- Study List -->
    <ul class="study-list list-unstyled">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="study-item">
                    <a class="study-title" data-bs-toggle="modal" data-bs-target="#studyModal<?php echo $row['study_id']; ?>">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                    <div class="study-author-year">
                        <?php echo htmlspecialchars($row['author']); ?> - <?php echo htmlspecialchars($row['year']); ?>
                    </div>
                    <div class="study-abstract">
                        <?php echo htmlspecialchars(substr($row['abstract'], 0, 150)) . '...'; ?>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="cite-button me-3" onclick="showCitationModal('<?php echo htmlspecialchars($row['author']); ?>', '<?php echo htmlspecialchars($row['title']); ?>', '<?php echo htmlspecialchars($row['year']); ?>')">
                            Cite
                        </div>

                        <!-- Add to Favorites Button -->
                        <button class="btn btn-sm" onclick="addToFavorites(<?php echo $row['study_id']; ?>)">
                            <i class="fas fa-star"></i> 
                        </button>
                    </div>

                </li>

                <!-- Study Modal -->
                <div class="modal fade" id="studyModal<?php echo $row['study_id']; ?>" tabindex="-1" aria-labelledby="studyModalLabel<?php echo $row['study_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="studyModalLabel<?php echo $row['study_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                                <p><strong>Abstract:</strong> <?php echo htmlspecialchars($row['abstract']); ?></p>
                                <p><strong>Keywords:</strong> <?php echo htmlspecialchars($row['keywords']); ?></p>
                                <p><strong>Year:</strong> <?php echo htmlspecialchars($row['year']); ?></p>
                                <p><strong>Call Number:</strong> <?php echo htmlspecialchars($row['cNumber']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No studies found for the BA course.</div>
        <?php endif; ?>
    </ul>
</div>
       <!-- Citation Modal -->
       <div class="modal fade" id="citationModal" tabindex="-1" aria-labelledby="citationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="citationModalLabel">Citation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="citationText" class="form-control" rows="3" readonly></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="copyCitation()">Copy Citation</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toSentenceCase(text) {
    return text.toLowerCase().replace(/(^\w{1}|\.\s*\w{1})/gi, letter => letter.toUpperCase());
}

function showCitationModal(author, title, year) {
    // Convert the title to sentence case for APA 7th edition
    const sentenceCaseTitle = toSentenceCase(title);
    
    // Generate the citation in APA 7th edition format with sentence case and additional text
    const citation = `${author} (${year}). ${sentenceCaseTitle}. *Unpublished undergrad thesis [ Northern Bukidnon State College ] Digi-Studies*.`;
    
    // Set the citation text in the textarea
    document.getElementById('citationText').value = citation;
    
    // Show the citation modal
    const citationModal = new bootstrap.Modal(document.getElementById('citationModal'));
    citationModal.show();
}

function copyCitation() {
    // Copy the citation text to clipboard
    const citationText = document.getElementById('citationText');
    citationText.select();
    document.execCommand('copy');

    // Notify the user
    alert('Citation copied to clipboard!');
}

function addToFavorites(study_id) {
    const user_id = <?php echo $_SESSION['user_id']; ?>;

    // Create an AJAX request to check if the study is already in favorites
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_favorite.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = xhr.responseText;
            if (response == 'exists') {
                alert('This study is already in your favorites!');
            } else {
                // Proceed to add to favorites
                addToFavoritesAction(study_id, user_id);
            }
        }
    };
    xhr.send('user_id=' + user_id + '&study_id=' + study_id);
}

function addToFavoritesAction(study_id, user_id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_to_favorites.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert('Study added to your favorites!');
        }
    };
    xhr.send('user_id=' + user_id + '&study_id=' + study_id);
}



    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
