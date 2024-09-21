<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Books</title>
    <link rel="stylesheet" href="sections.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f0f0;
        }

        .study {
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-weight: bold;
            font-size: 1.3rem;
        }

        .card-subtitle {
            font-size: 1rem;
            color: #6c757d;
        }

        .card-text {
            font-size: 0.95rem;
        }

        .see-more {
            cursor: pointer;
            color: blue;
        }

        .filter-section {
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .card-title {
                font-size: 1.1rem;
            }

            .card-subtitle {
                font-size: 0.9rem;
            }

            .card-text {
                font-size: 0.85rem;
            }
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
                    <a class="nav-link" href="../favorites.php">Favorites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../notification.php">Notifications</a>
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

<!-- PHP code starts here -->
<?php
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

// Variables for filtering
$identifier = isset($_POST['identifier']) ? $_POST['identifier'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';

// Fetch studies based on filters
$sql = "SELECT title, author, abstract, keywords, year FROM studiestbl WHERE type = 'BSIT'";

if ($identifier) {
    $sql .= " AND identifier = '$identifier'";
}

if ($year) {
    $sql .= " AND year = '$year'";
}

$result = $conn->query($sql);
?>

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

<section class="first">
    <div class="container">
        <h2>College of Computer Studies</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                // Output data for each study
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

<?php
$conn->close();
?>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
