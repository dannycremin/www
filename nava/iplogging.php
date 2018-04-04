<!DOCTYPE html>
<html>
<head>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
</head>
<body>


<?php

include "/var/iplogdatabasecreds.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT RemoteIP, Query, Time FROM iplog";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
		echo "<table><tr><th>Remote IP</th><th>Query</th><th>Time</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
		echo "<tr><td>" . $row["RemoteIP"]. "</td><td>" . $row["Query"]. "</td><td>" . $row["Time"]. "</td></tr>";
    }
} else {
    echo "0 results";
}
$conn->close();

?>