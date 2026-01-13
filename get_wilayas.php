<?php
$host = "localhost";
$user = "root";     // change if needed
$pass = "";         // change if needed
$db   = "landseifo";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, code, willaya, ar_name FROM willaya ORDER BY code ASC";
$result = $conn->query($sql);

$wilayas = [];
while ($row = $result->fetch_assoc()) {
    $wilayas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($wilayas);
?>
