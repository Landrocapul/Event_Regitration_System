<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE tbl_participants SET fullname = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fullname, $email, $id);

    if ($stmt->execute()) {
        header("Location: view.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>