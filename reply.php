<?php
$dotbit= $_POST["dotbit"];
$output = shell_exec("sudo /usr/bin/namecoind name_show d/$dotbit 2>&1");
echo "The IP to name mapping for <b>$dotbit.bit</b>";
$json=$output;
$djson= json_decode($json);
$test= str_replace("\"", "", $djson->value);
echo "<br><br>";
echo $test;
?>

<?php
$icann=$_POST["icann"];
$output = shell_exec("dig   $icann  2>&1");
echo "The IP to name mapping for  <b>$icann</b> ";
echo  "<br><br>";
echo $output;
echo"<br><br>";
$linkaddress = "https:\/\/".$icann;
echo "<a href='$linkaddress'>Click here to go to $icann</a>";
echo "<br><br>";
?>