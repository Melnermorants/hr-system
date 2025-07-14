<?php
$mysqli = new mysqli("db", "user", "hrms2025", "hrms"); // ✅ match docker-compose

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected to MySQL database successfully!";

$sql = "SELECT * FROM users";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    
    // Fetch column headers
    while ($field = $result->fetch_field()) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";

    // Fetch data rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $data) {
            echo "<td>{$data}</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No users found or query failed.</p>";
}

$mysqli->close();
?>
