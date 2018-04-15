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
    bottom: 0%;

    position: fixed;
 

    
}

</style>
</head>
<img style="float:left; padding-right:0px;padding-top:0px;" src="litlogo.png">
<body>
<br>
<br>
<br>
<br>
<h1>Blockchain DNS &amp; PowerDNS Query</h1>

<div class="container">
  <form action="reply.php" method="post">
    <label for="fname">Enter a domain name for resolution</label><br>
    <input type="text" id="queryinput" name="queryinput" placeholder="               Try google.com or cnsmfyp.bit">
	<br>
    <input type="submit" value="Submit">

  </form>
</div>


<div class="footer">

<p><b>Server IP: </b>164.132.102.42</p>


</div>
</body>
</html>
