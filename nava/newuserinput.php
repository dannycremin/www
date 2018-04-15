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
<body>

<h2>Blockchain / PowerDNS Query Engine</h2>

<div class="container">
  <form action="style.php" method="post">
    <label for="fname">Enter a .bit address to resolve</label><br>
    <input type="text" id="dotbit" name="dotbit" placeholder="Example: dotbitwhois">
	<br>

    <label for="lname">Enter a top level domain address to resolve</label><br>
    <input type="text" id="icann" name="icann" placeholder="Example: google.com">
	<br>
    <input type="submit" value="Submit">

  </form>
</div>


<div class="footer">

<p><b>Server: </b>server2.dan.com</p><b>IP Address: </b>54.38.34.58</p>


</div>
</body>
</html>
