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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-color">
      <div class="container-fluid">
        <a class="navbar-brand" href="./dashboard.php"><img src="imgs/book.png" height="70" alt=""> DIGI - BOOKS</a>
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
              <a class="nav-link" href="favorites.php">Favorites</a>
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
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>
