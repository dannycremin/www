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
echo "$dotbit . bit";
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
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>

