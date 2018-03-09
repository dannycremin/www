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
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM records where name='dannycremin.bit'";
$result = $conn->query($sql);

//if ($result->num_rows > 0) {
    // output data of each row
  //  while($row = $result->fetch_assoc()) {
    //    echo "<b>Name:</b> " . $row["name"]. " <b>Type:</b> ". $row["type"]. " <b>Content: </b>" . $row["content"]. "<br>";
//    }
//} else {
//    echo "0 results";
//}

if ($result->num_rows > 0) {
    echo "<table><tr><th>FQDN</th><th>Type</th><th>Content</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>". $row["name"]."</td><td>". $row["type"]. "</td>". "<td>" . $row["content"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}






$conn->close();
?>
