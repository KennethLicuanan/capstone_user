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

$servername = "localhost";
$username = "root";
$password = ""; // replace with your database password
$dbname = "capstonedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $abstract = mysqli_real_escape_string($conn, $_POST['abstract']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);


    // First insert into studytbl
    $sql = "INSERT INTO studytbl (title, author, abstract, keywords, year) VALUES ('$title', '$author', '$abstract', '$keywords', '$year')";
    if ($conn->query($sql) === TRUE) {
        $study_id = $conn->insert_id; // Get last inserted ID for studytbl

        // Now insert into categorytbl
        $sql_cat = "INSERT INTO categorytbl (study_id, course, type) VALUES ('$study_id', '$course', '$type')";
        if ($conn->query($sql_cat) === TRUE) {
            $cat_id = $conn->insert_id; // Get last inserted ID for categorytbl

            // Now insert into uploadtbl with the path to the uploaded file
            $sql_upload = "INSERT INTO uploadtbl (cat_id, path) VALUES ('$cat_id', '$targetFilePath')";
            if ($conn->query($sql_upload) === TRUE) {
                echo "Study added successfully with uploaded approval sheet.";
            } else {
                echo "Error in uploadtbl: " . $conn->error;
            }
        } else {
            echo "Error in categorytbl: " . $conn->error;
        }
    } else {
        echo "Error in studytbl: " . $conn->error;
    }
} else {
    echo "File upload failed. Please try again.";
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digi-Studies</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

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

        
        .form-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .form-title {
            font-weight: bold;
            font-size: 30px;
            margin-bottom: 20px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .form-label{
          font-weight: bolder;
          font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        /* Stylish border for input fields */
        .form-control {
            border: 1px solid black; /* Green border */
            padding: 10px;
            transition: box-shadow 0.3s ease;
        }

        .form-select {
            border: 1px solid black; /* Green border */
            padding: 10px;
            transition: box-shadow 0.3s ease;
        }
        
        /* Add shadow effect on focus */
        .form-control:focus {
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
            outline: none;
        }
        
        .input-group .plus-button {
            margin-left: -40px; /* Adjust for button positioning */
        }

        .img-preview {
            width: 100%;
            max-width: 200px;
            height: auto;
            margin-top: 10px;
        }
        .sidebar .sidebar-brand img {
            border-radius: 50%;
        }

        /* Adjusting modal body for better layout */
.modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
}
/* Optional: Add a specific size for the cropped image preview */
#capturedImage {
    max-width: 100%; /* Keep it responsive */
    width: 600px; /* Set a specific width */
    height: auto; /* Maintain aspect ratio */
    margin-top: 10px;
}


/* Optional: Add max-height for video and image in the modal */
#cameraFeed, #capturedImage {
    max-height: 300px; /* Set the maximum height */
    width: auto; /* Maintain aspect ratio */
}

    </style>
</head>
<body>

<div class="sidebar">
        <div class="sidebar-brand">
            <img src="imgs/logo.jpg" height="50" alt="Digi-Studies"> Digi - Studies
        </div>
        <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
        <a href="IT.php"><i class="fas fa-laptop"></i> College of Computer Studies</a>
        <a href="BA.php"><i class="fas fa-briefcase"></i> Business Administration</a>
        <a href="TEP.php"><i class="fas fa-chalkboard-teacher"></i> Teachers Education Program</a>
        <a href="add.php"><i class="fas fa-plus"></i> Add Study</a>
        <a href="manage.php"><i class="fas fa-tasks"></i> Manage Studies</a>
        <a href="user.php"><i class="fas fa-users"></i> User Logs</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    
<div class="content">
    <div class="container">
        <div class="form-card">
            <div class="form-title">Study Information</div>
            
            <!-- Title field with Plus Button -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="title" placeholder="Enter study title">
                    <input type="file" id="titleImage" accept="image/*" style="display: none;">
                    <i class="fas fa-plus plus-button" onclick="triggerFileInput('titleImage')"></i>
                </div>
            </div>
            
            <!-- Author field with Plus Button -->
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="author" placeholder="Enter author name">
                    <input type="file" id="authorImage" accept="image/*" style="display: none;">
                    <i class="fas fa-plus plus-button" onclick="triggerFileInput('authorImage')"></i>
                </div>
            </div>
            
            <!-- Abstract field with Plus Button -->
            <div class="mb-3">
                <label for="abstract" class="form-label">Abstract</label>
                <div class="input-group">
                    <textarea class="form-control" id="abstract" rows="4" placeholder="Enter abstract"></textarea>
                    <input type="file" id="abstractImage" accept="image/*" style="display: none;">
                    <i class="fas fa-plus plus-button" onclick="triggerFileInput('abstractImage')"></i>
                </div>
            </div>
            
            <!-- Keywords field with Plus Button -->
            <div class="mb-3">
                <label for="keywords" class="form-label">Keywords</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="keywords" placeholder="Enter keywords">
                    <input type="file" id="keywordsImage" accept="image/*" style="display: none;">
                    <i class="fas fa-plus plus-button" onclick="triggerFileInput('keywordsImage')"></i>
                </div>
            </div>
            
            <!-- Year field -->
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" placeholder="Enter study year">
            </div>
            
            <!-- Course selection -->
            <div class="mb-3">
                <label for="course" class="form-label">Select Course</label>
                <select id="course" class="form-select">
                    <option selected>Choose...</option>
                    <option value="IT">College of Computer Studies</option>
                    <option value="BA">Business Administration</option>
                    <option value="TEP">Teachers Education Program</option>
                </select>
            </div>
            
            <!-- Program selection -->
            <div class="mb-3">
                <label for="program" class="form-label">Select Program/Type of Study</label>
                <select id="program" class="form-select">
                    <option selected>Choose...</option>
                </select>
            </div>
            
            <!-- New photo upload section -->
            <div class="mb-3">
                <label for="studyPhoto" class="form-label">Approval Sheet</label>
                <input type="file" id="studyPhoto" accept="image/*" class="form-control" onchange="previewPhoto()">
                <img id="photoPreview" class="img-preview" src="#" alt="Photo Preview" style="display:none;">
            </div>
            
            <!-- Modal for Image Source Selection -->
<div class="modal fade" id="imageSourceModal" tabindex="-1" aria-labelledby="imageSourceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageSourceModalLabel">Select Image Source</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-primary w-100 mb-2" onclick="startCamera()">Use Camera</button>
                <button type="button" class="btn btn-secondary w-100" onclick="chooseFromGallery()">Choose from Gallery</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Camera Live Feed -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Camera Live Feed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="cameraFeed" width="100%" autoplay></video>
                <button type="button" class="btn btn-success mt-2" onclick="captureImage()">Capture</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Image Cropping -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Crop Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="capturedImage" alt="Captured Image" style="max-width: 100%;" />
                <button type="button" class="btn btn-primary mt-2" onclick="confirmCrop()">Confirm Crop</button>
            </div>
        </div>
    </div>
</div>


            <button id="addStudyButton" class="btn btn-primary" onclick="addStudy()" data-bs-toggle="popover" title="Success" data-bs-content="Study added successfully!">Add Study</button>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.0.2/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        const programOptions = {
            IT: ['IoT', 'Web-Based', 'Web-Application','Mobile Application'],
            BA: ['Financial Management', 'Operations Management', 'Marketing Management'],
            TEP: ['Early Childhood', 'Seconrady Education', 'Elementary Education']
        };

        document.getElementById('course').addEventListener('change', function() {
            const course = this.value;
            const programSelect = document.getElementById('program');
            programSelect.innerHTML = ''; // Clear existing options

            if (programOptions[course]) {
                programOptions[course].forEach(function(program) {
                    const option = document.createElement('option');
                    option.value = program;
                    option.textContent = program;
                    programSelect.appendChild(option);
                });
            } else {
                const defaultOption = document.createElement('option');
                defaultOption.textContent = 'Choose...';
                programSelect.appendChild(defaultOption);
            }
        });


// Track the input field currently being edited
let currentInputId = null;
let cropper;


// Open modal to select image source
function triggerFileInput(inputId) {
    currentInputId = inputId;
    const modal = new bootstrap.Modal(document.getElementById('imageSourceModal'));
    modal.show();
}

// Start the camera and set a lower resolution
function startCamera() {
    navigator.mediaDevices.getUserMedia({
        video: {
            width: { ideal: 640 }, // Lower resolution for better performance
            height: { ideal: 480 }
        }
    })
    .then((stream) => {
        const video = document.getElementById('cameraFeed');
        video.srcObject = stream;
        video.play();

        const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
        cameraModal.show();

        // Stop the camera stream when the modal is hidden
        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', () => {
            stream.getTracks().forEach(track => track.stop());
        });
    })
    .catch((error) => {
        alert('Unable to access camera. Please check your device settings.');
        console.error('Camera access error:', error);
    });
}


// Capture image and display it in the cropper
function captureImage() {
    const video = document.getElementById('cameraFeed');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageDataUrl = canvas.toDataURL('image/png');
    document.getElementById('capturedImage').src = imageDataUrl;

    // Open the cropping modal
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    cropModal.show();

    // Initialize Cropper.js on the captured image
    if (cropper) cropper.destroy(); // Destroy previous cropper instance if any
    const image = document.getElementById('capturedImage');
    cropper = new Cropper(image, {
        aspectRatio: NaN,
        viewMode: 1,
    });
}

// Confirm crop and recognize text
function confirmCrop() {
    const canvas = cropper.getCroppedCanvas();

    // Convert the cropped image to a data URL for Tesseract
    const croppedImageData = canvas.toDataURL('image/png');
    recognizeTextFromImageData(croppedImageData);

    // Hide the crop modal
    const cropModal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
    cropModal.hide();
}

// Text recognition with Tesseract.js
function recognizeTextFromImageData(imageData) {
    Tesseract.recognize(imageData, 'eng', {
        logger: (m) => console.log(m),
    })
    .then(({ data: { text } }) => {
        const cleanedText = text.trim().replace(/[\n\r]+/g, " ");
        if (cleanedText) {
            document.getElementById(currentInputId).value = cleanedText;
        } else {
            alert("No text detected in the image. Please ensure the image is clear and try again.");
        }
    })
    .catch((error) => {
        console.error('Text recognition error:', error);
        alert("Text recognition failed. Try improving the image clarity and lighting.");
    });
}



// Function to use the gallery instead of the camera
function chooseFromGallery() {
    document.getElementById(currentInputId).removeAttribute('capture'); 
    document.getElementById(currentInputId).click(); 
    const modal = bootstrap.Modal.getInstance(document.getElementById('imageSourceModal'));
    modal.hide();
}

        function recognizeText(inputId, fieldId) {
    const imageInput = document.getElementById(inputId);
    if (imageInput.files.length === 0) {
        alert('Please select an image file.');
        return;
    }

    const file = imageInput.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        const imageSrc = event.target.result;

        Tesseract.recognize(
            imageSrc,
            'eng',
            {
                logger: info => console.log(info) // Log progress
            }
        ).then(({ data: { text } }) => {
            // Trim whitespace and remove unwanted characters
            const cleanedText = text.trim().replace(/[\n\r]+/g, " ");
            document.getElementById(fieldId).value = cleanedText; // Set recognized text to the field
        }).catch(error => {
            console.error('Error during text recognition:', error);
        });
    };

    reader.readAsDataURL(file);
}


        // Event listeners for each file input
        document.getElementById('titleImage').addEventListener('change', function() {
            recognizeText('titleImage', 'title');
        });

        document.getElementById('authorImage').addEventListener('change', function() {
            recognizeText('authorImage', 'author');
        });

        document.getElementById('abstractImage').addEventListener('change', function() {
            recognizeText('abstractImage', 'abstract');
        });

        document.getElementById('keywordsImage').addEventListener('change', function() {
            recognizeText('keywordsImage', 'keywords');
        });

        // Function to preview the uploaded photo
        function previewPhoto() {
            const photoInput = document.getElementById('studyPhoto');
            const preview = document.getElementById('photoPreview');
            
            if (photoInput.files && photoInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(photoInput.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }


        function addStudy() {
        // Get form data
        const title = document.getElementById('title').value.trim();
        const author = document.getElementById('author').value.trim();
        const abstract = document.getElementById('abstract').value.trim();
        const keywords = document.getElementById('keywords').value.trim();
        const year = document.getElementById('year').value.trim();
        const course = document.getElementById('course').value;
        const type = document.getElementById('program').value;

        // Validate fields
        if (!title || !author || !abstract || !keywords || !year || !course || !type) {
            alert('Please fill in all fields');
            return;
        }

        // Prepare data to send
        const formData = new FormData();
        formData.append('title', title);
        formData.append('author', author);
        formData.append('abstract', abstract);
        formData.append('keywords', keywords);
        formData.append('year', year);
        formData.append('course', course);
        formData.append('type', type);

        // Send data to PHP script using AJAX
        fetch('add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                // Trigger Bootstrap popover when study is added successfully
                const popoverTrigger = new bootstrap.Popover(document.getElementById('addStudyButton'));
                popoverTrigger.show();

                // Optionally hide the popover after a few seconds
                setTimeout(() => {
                    popoverTrigger.hide();
                }, 3000);
            } else {
                console.error('Error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }



    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
