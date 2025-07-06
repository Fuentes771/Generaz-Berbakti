<?php
header('Content-Type: text/html');
date_default_timezone_set('Asia/Jakarta');

$conn = new mysqli("localhost", "root", "", "tsunami_warning");

// Query to get logs with proper timestamp handling
$result = $conn->query("
    SELECT 
        IFNULL(timestamp, NOW()) as log_time,
        event_type, 
        vibration, 
        status 
    FROM event_logs 
    ORDER BY timestamp DESC 
    LIMIT 10
");

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $time = new DateTime($row['log_time']);
        echo "<tr>";
        echo "<td>" . $time->format('Y-m-d H:i:s') . "</td>";
        echo "<td>" . htmlspecialchars($row['event_type']) . "</td>";
        echo "<td>" . $row['vibration'] . "</td>";
        echo "<td class='status-" . strtolower($row['status']) . "'>" . $row['status'] . "</td>";
        echo "<td><button class='btn btn-sm btn-outline-info'>Details</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No events recorded</td></tr>";
}

$conn->close();
?>