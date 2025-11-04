<?php
require 'db_connect.php';

$message = '';
$edit_participant = null;

// --- Handle ALL Form Submissions (POST Requests) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Handle REGISTRATION ---
    if (isset($_POST['register'])) {
        $count_sql = "SELECT COUNT(id) AS total FROM tbl_participants";
        $count_result = $conn->query($count_sql);
        $count_row = $count_result->fetch_assoc();
        
        if ($count_row['total'] < 10) {
            $stmt = $conn->prepare("INSERT INTO tbl_participants (fullname, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $_POST['fullname'], $_POST['email']);
            if ($stmt->execute()) {
                $message = "<div class='message message-success'>Registration successful!</div>";
            } else {
                $message = "<div class='message message-error'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }

    // --- Handle UPDATE ---
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE tbl_participants SET fullname = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $_POST['fullname'], $_POST['email'], $_POST['id']);
        if ($stmt->execute()) {
            $message = "<div class='message message-success'>Participant updated successfully!</div>";
        } else {
            $message = "<div class='message message-error'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// --- Check for Edit Mode (GET Request) ---
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT id, fullname, email FROM tbl_participants WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_participant = $result->fetch_assoc();
    }
    $stmt->close();
}


// --- Fetch Current Participant Count for Display ---
$sql = "SELECT COUNT(id) AS total_participants FROM tbl_participants";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_participants = $row['total_participants'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management Dashboard</title>
    <link rel="stylesheet" href="creative-yellow-blue.css">
</head>
<body>
<h1 class="main-title">Event Registration System</h1>
<div class="dashboard-container">

    <!-- ========== COLUMN 1: REGISTRATION FORM ========== -->
    <div class="column column-form">
        <h2>Event Registration</h2>
        
        <?php if (!$edit_participant) { echo $message; } // Show messages here if not in edit mode ?>

        <?php if ($total_participants < 10) : ?>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register Now</button>
            </form>
            <p style="text-align:center; margin-top:15px;">
                <b><?php echo 10 - $total_participants; ?></b> spots remaining.
            </p>
        <?php else : ?>
            <div class="message message-info">Registration is Full!</div>
        <?php endif; ?>
    </div>

    <!-- ========== COLUMN 2: PARTICIPANTS TABLE ========== -->
    <div class="column column-table">
        <h2>Registered Participants</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $participants_sql = "SELECT id, fullname, email FROM tbl_participants ORDER BY date_registered DESC";
                    $participants_result = $conn->query($participants_sql);
                    if ($participants_result->num_rows > 0) {
                        while($p_row = $participants_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $p_row["id"] . "</td>";
                            echo "<td>" . htmlspecialchars($p_row["fullname"]) . "</td>";
                            echo "<td>" . htmlspecialchars($p_row["email"]) . "</td>";
                            echo "<td class='action-links'>";
                            echo "<a class='btn btn-action btn-success' href='index.php?edit_id=" . $p_row['id'] . "'>Edit</a>";
                            echo "<a class='btn btn-action btn-delete' href='delete.php?id=" . $p_row['id'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>No participants yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========== COLUMN 3: EDIT FORM (Conditional) ========== -->
    <?php if ($edit_participant): ?>
    <div class="column column-edit">
        <h2>Edit Participant</h2>
        <?php if ($edit_participant) { echo $message; } // Show update messages here ?>
        <form action="index.php" method="post">
            <input type="hidden" name="id" value="<?php echo $edit_participant['id']; ?>">
            <div class="form-group">
                <label for="edit-fullname">Full Name:</label>
                <input type="text" id="edit-fullname" name="fullname" value="<?php echo htmlspecialchars($edit_participant['fullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="edit-email">Email:</label>
                <input type="email" id="edit-email" name="email" value="<?php echo htmlspecialchars($edit_participant['email']); ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update Participant</button>
            <a href="index.php" class="btn btn-secondary btn-action" style="margin-top: 10px; width: 93%;">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

</div>

<?php $conn->close(); ?>
</body>
</html>