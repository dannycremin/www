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