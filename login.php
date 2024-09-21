<?php
session_start();

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "capstonedb";

// Default admin credentials
$admin_username = "admin";
$admin_password = "admin12345";

// Create a connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_error = ""; // Initialize an empty error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the credentials match the hardcoded admin account
    if ($username === $admin_username && $password === $admin_password) {
        // Store admin information in session
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = true; // Set an admin flag

        

        echo "<script>alert('Login successful! Welcome Administrator...'); window.location.href = 'admin.php';</script>";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT user_id FROM usertbl WHERE credentials = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);

    // Execute the statement
    $stmt->execute();

    // Store the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user ID
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        // Store user information in session
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Log user login activity
        $activity = "User logged in";
        $stmt = $conn->prepare("INSERT INTO activitylog_tbl (user_id, activity, date) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $activity);
        $stmt->execute();

        echo "<script>alert('Login successful! Welcome to DIGIBOOKS...'); window.location.href = 'dashboard.php';</script>";
        exit();
    } else {
        $login_error = "Invalid username or password.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { 
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; 
        background-image: url('./imgs/background.jpg'); 
        background-size: cover; /* Ensures the image covers the entire body */
        background-repeat: no-repeat; /* Prevents the image from repeating */
        background-position: center; /* Centers the image */
        }
        .login-container { 
            width: 300px; 
            margin: auto; 
            margin-top: 55px; 
            padding: 20px;
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 20); /* Box shadow */
            border-radius: 8px; /* Rounded corners */
            background-color: white;
        }
        input[type=text], input[type=password] { 
            width: 90%; 
            padding: 10px; 
            margin: 5px 0;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        input[type=submit] { 
            width: 50%; 
            padding: 10px; 
            background-color: blue; 
            color: white; 
            border: none; 
            margin-left: 27%;
        }
        .login-container img {
            margin-left: 27%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
    </style>
    <script>
        function showError(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <?php
    if (!empty($login_error)) {
        echo "<script>showError('$login_error');</script>";
    }
    ?>
    <div class="login-container">
        <img src="imgs/book.png" height="150" alt=""><br>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="username">Credentials</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
