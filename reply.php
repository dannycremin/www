<?php

// Check if the entered domain is .bit or a regular TLD - dig based on the result.

$queryentry= $_POST["queryinput"];

if (strpos($queryentry, '.bit') !== false) {
	
	$dotbitquery= str_replace(".bit", "", $queryentry); 
	$dotbitqueryresult= shell_exec("sudo /usr/bin/namecoind name_show d/$dotbitquery 2>&1");
	echo "<pre>$dotbitqueryresult</pre>";
	
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
	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', 'localhost localhost 1','SOA',86400,NULL)
	AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='SOA')";	   

	if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	
	echo "<br><br>";
	
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

	
// Run pdnssec on the created zone to rectify-zone

	$rectifyzoneoutput = shell_exec("sudo /usr/bin/pdnssec rectify-zone $dotbitdns 2>&1");

// Output a successful rectify of zone
	
	echo "<b>Successfully rectified zone</b>";
	
	echo "<br><br>";
	
	echo "<pre>$rectifyzoneoutput</pre>";

// Final - dig out the .bit query from pdns

	$dotbitfinaldig = shell_exec("dig $dotbitdns @127.0.0.1 -p 54 2>&1");
	echo "<b>Output of DNS query</b>";
	echo "<br>";
	echo "<pre>$dotbitfinaldig</pre>";
	
} else {
	
	$tldqueryresult = shell_exec("dig   $queryentry  2>&1");
	echo "<h3>DNS information for $queryentry</h3>";
	echo  "<br><br>";
	echo "<pre>$tldqueryresult</pre>";	
}
?>




<?php
/*
include "/var/databasecreds.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Add SOA record based on .bit query but check if it already exists first.

$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='SOA')";	   

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
*/
?> 

<?php
/*
include "/var/databasecreds.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Add A record based on .bit query but check if it already exists first.

$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', '$dotbitip','A',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='A')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
*/
?>

<?php
/*
//to get this to work, add www-data with access to /usr/bin/pdnssec in sudoers file

$rectifyzoneoutput = shell_exec("sudo /usr/bin/pdnssec rectify-zone $dotbitdns 2>&1");
echo "<pre>$rectifyzoneoutput</pre>";
*/
?>

<?php
/*
// Check if the entered request is .bit or a regular TLD - dig based on the result.

$queryfinaldig= $_POST["queryinput"];

if (strpos($queryfinaldig, '.bit') !== false) {
	
	$dotbitfinaldig = shell_exec("dig $queryfinaldig @127.0.0.1 -p 54 2>&1");
	echo "<h3>.bit test</h3>";
	echo  "<br><br>";

	echo "<pre>$dotbitfinaldig</pre>";

} else {
	$tldfinaldig = shell_exec("dig $queryfinaldig 2>&1");
	echo "<h3>TLD test</h3>";
	echo "<pre>$tldfinaldig</pre>";
}
*/
?>


<?php
/*

// Then dig out the newly created record

$output = shell_exec("dig $dotbitdns @127.0.0.1 -p 54 2>&1");
echo  "<br><br>";
echo "<pre>$output</pre>";
?>

<?php

$dotbituserentry= $_POST["dotbit"];
$dotbit= str_replace(".bit", "", $dotbituserentry); 
$output = shell_exec("sudo /usr/bin/namecoind name_show d/$dotbit 2>&1");
echo "The IP to name mapping for <b>$dotbit.bit</b>";
$json=$output;
$djson= json_decode($json);
$ipoutput= str_replace("\"", "", $djson->value);
echo "<br><br>";
echo $ipoutput;
echo "<br><br>";
$dotbitweboutput = "$dotbit.bit";
echo "$dotbitweboutput";
echo "<br><br>";
?>

<?php


$icann=$_POST["icann"];
$output = shell_exec("dig   $icann  2>&1");
echo "The IP to name mapping for  <b>$icann</b> ";
echo  "<br><br>";
echo "<pre>$output</pre>";
echo"<br><br>";
// $linkaddress = "https:\/\/".$icann;
//echo "<a href='$linkaddress'>Click here to go to $icann</a>";
echo "<br><br>";
?>


<?php
include "/var/databasecreds.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
	   
$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitweboutput', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitweboutput' AND type='SOA')";	   

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>


<?php
include "/var/databasecreds.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitweboutput', '$ipoutput','A',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitweboutput' AND type='A')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>




<?php

//to get this to work, add www-data with access to /usr/bin/pdnssec in sudoers file

$rectifyzoneoutput = shell_exec("sudo /usr/bin/pdnssec rectify-zone $dotbitweboutput 2>&1");
echo "<pre>$rectifyzoneoutput</pre>";
?>

<?php
// Then dig out the newly created record

$output = shell_exec("dig $dotbitweboutput @127.0.0.1 -p 54 2>&1");
echo  "<br><br>";
echo "<pre>$output</pre>";
?>



<?php

// Check if the entered domain is .bit or a regular TLD - dig based on the result.

$wildcardentry= $_POST["wildcardtextbox"];

if (strpos($wildcardentry, '.bit') !== false) {
	
	$output1 = shell_exec("dig $wildcardentry @127.0.0.1 -p 54 2>&1");
	echo "<h3>.bit test</h3>";
	echo  "<br><br>";

	echo "<pre>$output1</pre>";

} else {
	$output2 = shell_exec("dig $wildcardentry 2>&1");
	echo "<h3>TLD test</h3>";
	echo "<pre>$output2</pre>";
}

*/
?>




