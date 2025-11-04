<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant | Creative</title>
    <link rel="stylesheet" href="creative-style.css">
</head>
<body>

<div class="container">
    <h2>Edit Participant Information</h2>

    <?php
    require 'db_connect.php';

    $id = $_GET['id'];
    $sql = "SELECT fullname, email FROM tbl_participants WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    ?>

    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($row['fullname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Information</button>
    </form>

    <?php
    $stmt->close();
    $conn->close();
    ?>
     <div class="page-link">
        <a href="index.php">Back to Participants List</a>
    </div>
</div>

</body>
</html>