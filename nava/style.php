<style>
body {font-family: Arial, Helvetica, sans-serif; text-align: left;}


.parent
{
	width:100%;
}
.left
{
	float:left;
	width:50%;
	box-sizing:border-box;
	padding:5px;
	border:#CCC solid 0px;
	min-height:50px;
}

.right
{
	float:left;
	width:50%;
	box-sizing:border-box;
	padding:5px;
	border:#CCC solid 0px;
	min-height:200px;
}

.leftbottom
{
	float:left;
        width:50%;
        box-sizing:border-box;
        padding:5px;
        border:#CCC solid 0px;
        min-height:200px;
}

.rightbottom
{
	float:left;
        width:50%;
        box-sizing:border-box;
        padding:5px;
        border:#CCC solid 0px;
        min-height:200px;
}


.footer {
    bottom: 0%;

    position: fixed;
}

</style> 

<div class="parent">
</div>
<div class="left">

<?php
$dotbit= $_POST["dotbit"];
$output = shell_exec(" time sudo /usr/bin/namecoind name_show d/$dotbit 2>&1");
echo "<h3>The IP to name mapping for <b>$dotbit.bit</b></h3>";

echo "<pre>$output</pre>";


?>

    </div>

<div class="right">
<?php
$icann=$_POST["icann"];
$output = shell_exec("dig @127.0.0.1  $icann  2>&1");
echo "<h3>The IP to name mapping for  <b>$icann</h3></b> ";
echo "<pre>$output</pre>";
//echo"<br><br>";
$linkaddress = "https:\/\/".$icann;
//echo "<a href='$linkaddress'>Click here to go to $icann</a>";
//echo "<br><br>";
?>

    </div>

<div class="leftbottom">

<?php
echo "<h3>Public IP addresses & latest .bit/TLD requests</h3>"; 
$icann=$_POST["icann"];
$dotbit=$_POST["dotbit"];
$my_file = 'ip.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);
//$data = 'This is the data' . "\n";
$request = 'User IP address for TLD domain: ' .$icann .' and .bit: ' .$dotbit .'.bit requests' .' is:  '. $_SERVER['REMOTE_ADDR'] . "\r\n";
//$data = "$icann";
fwrite($handle, $request);
echo nl2br(file_get_contents("ip.txt"));
?>
</div>

<div class="rightbottom">
<?php
echo "<h3>Latency of requests</h3>";
$icann = $_POST["icann"];
$dotbit = $_POST["dotbit"];
$output = shell_exec("time dig +short $icann 2>&1");
echo "<pre>$output</pre>";
?>


 </div>

<div class="footer">

<p><b>Server: </b>server1.dan.com</p><b>IP Address: </b>164.132.103.42</p><a href="https://server1.dan.com">Go Back</a>



</div>

