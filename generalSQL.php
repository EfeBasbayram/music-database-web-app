<?php
// generalSQL.php

// Database connection settings - CHANGE THESE!
$servername = "localhost";
$username = "root";
$password = ""; // your db password
$dbname = "efe_basbayram"; // your db name

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the query from POST
$query = isset($_POST['custom_query']) ? $_POST['custom_query'] : '';

function isDangerousQuery($query) {
    // Basic check to prevent dangerous queries
    $dangerous = ['insert', 'update', 'delete', 'drop', 'alter', 'truncate'];
    foreach ($dangerous as $word) {
        if (stripos($query, $word) !== false) {
            return true;
        }
    }
    return false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Query Results</title>
    <style>
        body { background: #1a1a2e; color: white; font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #4cc9f0; }
        th { background: #0f3460; color: #4cc9f0; }
        a { color: #4cc9f0; text-decoration: none; }
        .error { color: #ff4c4c; }
        .container { max-width: 1200px; margin: 0 auto; }
        .back-btn { background-color:#16213e;color:#4cc9f0;text-decoration:none;font-weight:bold;border-radius:8px;padding:0.5rem 1.2rem;display:inline-block;margin-bottom:1.5rem;transition:background 0.18s,color 0.18s,transform 0.1s;}
    </style>
</head>
<body>
<div class="container">
    <a href="generalSQL.html" class="back-btn">← Back to Dashboard</a>
    <h1>Query Results</h1>
    <?php
    if (!$query) {
        echo "<p class='error'>No query provided.</p>";
    } elseif (isDangerousQuery($query)) {
        echo "<p class='error'>Dangerous queries (INSERT, UPDATE, DELETE, DROP, etc.) are not allowed.</p>";
    } else {
        $result = $conn->query($query);
        if (!$result) {
            echo "<p class='error'>Error: " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {
            echo "<table><tr>";
            // Output table headers
            while ($fieldinfo = $result->fetch_field()) {
                echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
            }
            echo "</tr>";
            // Output table rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No results found.</p>";
        }
    }
    $conn->close();
    ?>
</div>
</body>
</html> 