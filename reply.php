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

// $sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio)
// VALUES (2,'$dotbitweboutput','localhost localhost 1','SOA',86400,NULL),
//   (2,'$dotbitweboutput','$ipoutput','A',120,NULL)";
	   
$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitweboutput', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitweboutput' AND type='SOA')";	   

// "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitweboutput', '$ipoutput','A',86400,NULL)
// AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitweboutput' AND type='A')";

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
// If statement for TLD or .bit
$wilcardentry= $_POST["wildcardtextbox"];

if (strpos($wilcardentry, '.bit') !== false) {
	echo 'true';
}
?>





