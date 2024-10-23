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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="imgs/logo.jpg" height="50" alt="Digi-Studies"> Digi - Studies
        </div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="./sections/IT.php"><i class="fas fa-laptop"></i> College of Computer Studies</a>
        <a href="./sections/BA.php"><i class="fas fa-briefcase"></i> Business sectionsistration</a>
        <a href="./sections/TEP.php"><i class="fas fa-chalkboard-teacher"></i> Teachers Education Program</a>
        <a href="add_favorite.php"><i class="fas fa-star"></i> Favorites</a>
        <a href="#"><i class="fas fa-bell"></i> Notifications</a>
        <a href="help.php"><i class="fas fa-pencil"></i> Help</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
<div class="content">
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
  </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>