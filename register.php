<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tbl_participants (fullname, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $fullname, $email);

    if ($stmt->execute()) {
        header("Location: view.php"); // Redirect to the view page after successful registration
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>