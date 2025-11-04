<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Participants | Creative</title>
    <link rel="stylesheet" href="creative-style.css">
</head>
<body>

<div class="container">
    <h2>Registered Participants</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Date Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require 'db_connect.php';

            $sql = "SELECT id, fullname, email, date_registered FROM tbl_participants ORDER BY date_registered DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . $row["date_registered"] . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a class='btn btn-edit' href='edit.php?id=" . $row['id'] . "'>Edit</a>";
                    echo "<a class='btn btn-delete' href='delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No participants registered yet.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="page-link">
        <a href="index.php">Back to Registration</a>
    </div>
</div>

</body>
</html>