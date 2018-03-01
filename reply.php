<?php
$dotbit= $_POST["dotbit"];
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

$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio)
VALUES (2,'$dotbitweboutput','localhost localhost 1','SOA',86400,NULL),
	   (2,'$dotbitweboutput','$ipoutput','A',120,NULL)";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

<?php

$rectifyzoneoutput = shell_exec("pdnssec rectify-zone $dotbitweboutput 2>&1");
echo "<pre>$rectifyzoneoutput</pre>";
?>


