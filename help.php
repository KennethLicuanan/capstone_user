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
            /* General instructions container style */
    .instructions {
        margin: 30px auto;
        padding: 30px;
        background-color: #f5f7fa;
        border-radius: 8px;
        max-width: 800px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: left;
        color: #333;
    }

    /* Title styling */
    .instructions h3 {
        color: #2c3e50;
        font-weight: bold;
        font-size: 26px;
        margin-bottom: 15px;
        text-align: center;
        border-bottom: 2px solid #d1d5db;
        padding-bottom: 10px;
    }

    /* List styling */
    .instructions ul {
        list-style-type: none;
        padding: 0;
    }

    /* List items styling */
    .instructions ul li {
        display: flex;
        align-items: flex-start;
        margin: 15px 0;
        font-size: 18px;
        line-height: 1.6;
        color: #555;
        font-weight: bold;
    }

    /* Icon for each list item */
    .instructions ul li::before {
        content: '\f05a'; /* Font Awesome info-circle icon */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #2d9cdb;
        margin-right: 10px;
    }

    /* Text adjustments */
    .instructions ul li span {
        flex: 1;
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
        content{
          
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
        <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
        <a href="help.php"><i class="fas fa-pencil"></i> Help</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
<div class="content">
<div class="container instructions">
    <h3>How to Use Digi-Books</h3>
    <ul>
        <li><span>Access "Courses" to view studies by course.</span></li>
        <li><span>Each course is organized by year.</span></li>
        <li><span>Use the filtering feature for a more specific search.</span></li>
        <li><span>Search using keywords for quick results.</span></li>
        <li><span>Studies are available from 2019 to 2024.</span></li>
        <li><span>Add studies to your favorites to view them easily in the favorites section.</span></li>
        <li><span>Click "Digi-Books" to return to the dashboard.</span></li>
    </ul>
</div>

  </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>