<?php
$host = "localhost";
$user = "root";     // change if needed
$pass = "";         // change if needed
$db   = "landseifo";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$wilaya_id = isset($_GET['wilaya_id']) ? intval($_GET['wilaya_id']) : 0;

$sql = "SELECT id, name, ar_name FROM communes WHERE id_willaya = $wilaya_id ORDER BY name ASC";
$result = $conn->query($sql);

$communes = [];
while ($row = $result->fetch_assoc()) {
    $communes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($communes);
?>
