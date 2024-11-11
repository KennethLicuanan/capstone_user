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

$result = $conn->query($sql);
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
        .node-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }
        .node {
            width: 150px;
            height: 150px;
            background-color: #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
        }
        .node:hover {
            background-color: #343a40;
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

    <!-- Search Form -->
    <form action="" method="get" class="mb-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search by Title, Author, or Keywords">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <div class="node-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="node" data-bs-toggle="modal" data-bs-target="#studyModal<?php echo $row['study_id']; ?>">
                    <span><?php echo htmlspecialchars($row['title']); ?></span>
                </div>

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
                
                <!-- APA Citation Button -->
                <button onclick="showCitationModal('<?php echo addslashes($row['author']); ?>', '<?php echo addslashes($row['title']); ?>', '<?php echo $row['year']; ?>')" class="btn btn-secondary mt-3">
                    Generate APA Citation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- APA Citation Modal -->
<div class="modal fade" id="citationModal" tabindex="-1" aria-labelledby="citationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="citationModalLabel">APA 6th Edition Citation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Copy the citation below:</p>
                <textarea id="citationText" class="form-control" rows="3" readonly></textarea>
                <button onclick="copyCitation()" class="btn btn-primary mt-3">Copy to Clipboard</button>
            </div>
        </div>
    </div>
</div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No studies found for the IT course.</div>
        <?php endif; ?>
    </div>
</div>

<script>
function showCitationModal(author, title, year) {
    // Generate the citation in APA 6th format
    const citation = `${author} (${year}). ${title}. Digi-Studies.`;

    // Set the citation text in the modal's textarea
    document.getElementById('citationText').value = citation;

    // Show the citation modal
    const citationModal = new bootstrap.Modal(document.getElementById('citationModal'));
    citationModal.show();
}

function copyCitation() {
    // Select and copy the citation text
    const citationText = document.getElementById('citationText');
    citationText.select();
    document.execCommand('copy');

    // Notify the user that the text has been copied
    alert('Citation copied to clipboard!');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
