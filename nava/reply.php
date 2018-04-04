<!DOCTYPE html>
<html>
<head>

<style>
table, th, td {
    border: 1px solid black;
}

html {
  font-family: sans-serif;
  -ms-text-size-adjust: 100%;
  -webkit-text-size-adjust: 100%;
}


</style>
</head>
<body>




<?php

// Check if the entered domain is .bit or a regular TLD - dig based on the result.
// If user enters www. on record strip it.
// If user enters .bit on query strip it

$prestringlower= $_POST["queryinput"];
// make the string lowercase
$queryentry= strtolower($prestringlower);

if (strpos($queryentry, '.bit') !== false) {
	
	// If user enters www. on record strip it and pass to $querystripwww variable
	
	// TESTING CODE BELOW
	
	echo "<b>Output before www. removed</b>";
	echo "<br><br>";
	echo $queryentry;
	echo "<br><br>";
	
	// TESTING CODE ENDS
	
	$querystripwww= str_replace("www.", "", $queryentry);
	
	// TESTING CODE BELOW
	
	echo "<b>Output after www. removed</b>";
	echo "<br><br>";
	echo $querystripwww;
	echo "<br><br>";
	
	// TESTING CODE ENDS
	
	
	// If user enters .bit on query strip it out and pass to $dotbitquery variable
	
	// TESTING CODE BELOW
	
	echo "<b>Output before .bit removed</b>";
	echo "<br><br>";
	echo $querystripwww;
	echo "<br><br>";
	
	// TESTING CODE ENDS
	
	$dotbitquery= str_replace(".bit", "", $querystripwww); 
	
	// TESTING CODE BELOW
	
	echo "<b>Output after .bit removed</b>";
	echo "<br><br>";
	echo $dotbitquery;
	
	// TESTING CODE ENDS
	
	
	// TESTING CODE BELOW
	
	echo "<b>JSON response for $dotbitquery</b>";
	echo "<br><br>";
	
	$dotbitqueryresult= shell_exec("sudo /usr/bin/namecoind name_show d/$dotbitquery 2>&1");
	echo "<pre>$dotbitqueryresult</pre>";
	
	// TESTING CODE ENDS
	
	$json= $dotbitqueryresult;
	$decodedjson= json_decode($json);
	$dotbitip= str_replace("\"", "", $decodedjson->value);
	$dotbitdns = "$dotbitquery.bit";
	
	// Output the IP address pulled from blockchain
	
	echo "<b>IP address pulled from blockchain</b>";
	
	echo "<br><br>";
	
	echo $dotbitip;
	
	echo "<br><br>";
	
	// Output the FQDN from the blockchain
	
	echo "<b>FQDN pulled from the blockchain</b>";
	
	echo "<br><br>";
	
	echo $dotbitdns;
	
	echo "<br><br>";

// Open SQL connection to add SOA record based on .bit query but check if it already exists first.
	
// Output the successful addition of the SOA record
	
	echo "<b>Check if SOA record was added to MySQL database</b>";
	
	echo "<br><br>";
	
	include "/var/databasecreds.php";	
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}
	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', 'admin@$dotbitdns','SOA',86400,NULL) AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='SOA')";	

	if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	
	echo "<br><br>";
	
	
/*

//Below is code to display table of PDNS results 

include "/var/databasecreds.php";	

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM records where name='$dotbitdns' and type='SOA'";
$result = $conn->query($sql);

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

*/
	
// Open a 2nd SQL connection to add A record based on .bit query but check if it already exists first.	

	// Output the successful addition of the SOA record
	
	echo "<b>Check if A record was added to MySQL database</b>";
	
	echo "<br><br>";

	include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', '$dotbitip','A',86400,NULL)
	AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='A')";

	if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	echo "<br><br>";

// Open a 3nd SQL connection to add A record to add www. to the beginning of the newly added .bit A record entry.

	// Output the successful addition of the www.record.bit output.
	
	echo "<b>Check if www. dotbit address was added</b>";
	
	echo "<br><br>";

	include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'www.$dotbitdns', '$dotbitip','A',86400,NULL)
	AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='www.$dotbitdns' AND type='A')";

	if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	echo "<br><br>";	
	
	
// Run pdnssec on the created zone to rectify-zone

	$rectifyzoneoutput = shell_exec("sudo /usr/bin/pdnssec rectify-zone $dotbitdns 2>&1");

// Output a successful rectify of zone
	
	echo "<b>Successfully rectified zone</b>";
	
	echo "<br><br>";
	
	echo "<pre>$rectifyzoneoutput</pre>";

// Final - dig out the .bit query from pdns

	$dotbitfinaldig = shell_exec("dig $queryentry @127.0.0.1 -p 54 2>&1");
	echo "<b>Output of DNS query</b>";
	echo "<br>";
	echo "<pre>$dotbitfinaldig</pre>";
	
} else {
	
	$tldqueryresult = shell_exec("dig   $queryentry  2>&1");
	echo "<h3>DNS information for $queryentry</h3>";
	echo  "<br>";
	echo "<pre>$tldqueryresult</pre>";	
}

//Code to remove records from database

include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "DELETE from records where domain_id = 2";

	if (mysqli_query($conn, $sql)) {
    echo "All records removed from database";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);

// Log remote IP address & query.
$remoteipaddress = $_SERVER['REMOTE_ADDR'];

	
include "/var/iplogdatabasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO iplog (RemoteIP, Query) VALUES ('$remoteipaddress', '$queryentry')";

	if (mysqli_query($conn, $sql)) {
    echo "Added remote IP & query to MySQL database" . "<br>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);

echo "Logging details" . "<br><br>";

include "/var/iplogdatabasecreds.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT RemoteIP, Query FROM iplog";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
		echo "<b>Remote IP:</b> " . $row["RemoteIP"]. " <b>Query:</b> " . $row["Query"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();


?>	
	



