<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif; text-align: center;}

input[type=text], select, textarea {
    width: 25%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-top: 6px;
    margin-bottom: 16px;
    resize: vertical;
}

input[type=submit] {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
	
}

input[type=submit]:hover {
    background-color: #45a049;
	
}


.container {
    border-radius: 5px;
    background-color: #ffffff;
    padding: 20px;
}

.footer {
    bottom: 1%;
    border: 1px solid black;
    padding: 0 6px;
    border-collapse: collapse;
    position: fixed;
    left: 950px;
}

</style>
</head>
<body>

<h2>Blockchain Backed DNS</h2>
<img src="/litlogo.png" style="float: left;" />
<div class="container">
  <form action="reply.php" method="post">
    <label for="fname">Enter a domain name</label><br>
    <input type="text" id="queryinput" name="queryinput" placeholder="google.com/cnsmfyp.bit">
	<br>
    <input type="submit" value="Submit">

  </form>
</div>
<div class="footer">

<?php


echo "<h2>IP/Query Logging</h2>";

include "/var/iplogdatabasecreds.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT RemoteIP, Query, Time FROM iplog DSEC LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
		echo "<table><tr><th>Remote IP </th><th>Query </th><th>Time </th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
		echo "<tr><td>" . $row["RemoteIP"]. "</td><td>" . $row["Query"]. "</td><td>" . $row["Time"]. "</td></tr>";
    }
} else {
    echo "0 results";
}
$conn->close();

?>
</div>

<!--
<div class="footer">

<p><b>Server IP: </b>164.132.103.42</p>


</div> -->
</body>
</html>
