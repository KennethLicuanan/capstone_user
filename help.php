<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}
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
          background-color: #eeeeee;
        }
        .instructions {
          margin: 30px auto;
          padding: 30px;
          background-color: #ffffff;
          border-radius: 15px;
          box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
          max-width: 800px;
          text-align: center;
          animation: fadeIn 1s ease-in-out;
        }
        .instructions h3 {
          color: #2c3e50;
          font-weight: bold;
          margin-bottom: 20px;
          font-size: 28px;
        }
        .instructions ul {
          list-style-type: none;
          padding: 0;
        }
        .instructions ul li {
          margin: 10px 0;
          font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
          font-size: 18px;
          font-weight: bold;
          color: #34495e;
          padding: 10px;
          background-color: #ecf0f1;
          border-radius: 8px;
          transition: transform 0.2s;
        }
        .instructions ul li:hover {
          transform: scale(1.05);
          background-color: #d0dff0;
        }
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-color">
      <div class="container-fluid">
        <a class="navbar-brand" href="./dashboard.php"><img src="imgs/book.png" height="70" alt=""> Digi - Studies</a>
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
                <li><a class="dropdown-item" href="./sections/TEP.php">Teacehrs Education Program</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./add_favorite.php">Favorites</a>
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

    <div class="container instructions">
        <h3>How to Use Digi-Books</h3>
        <ul>
            <li>You can press the "Courses" dropdown to see the studies of each course.</li>
            <li>Each course is categorized by year.</li>
            <li>You can use the filtering feature to narrow down your search for specific studies.</li>
            <li>Use keywords to find specific studies quickly.</li>
            <li>Uploaded studies are available from the years 2019 to 2024.</li>
            <li>You can add your favorite studies. And view it on the favorite section.</li>
            <li>You can navigate back to dashboard by clicking the word "Digi-Books".</li>
        </ul>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>