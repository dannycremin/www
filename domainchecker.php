
<?php

// Code below will check the query entry against the domains file to check that is a valid domain before processing


 $domainchecker = "domains.txt";

$domainsearch = "google.com";
                if(exec('grep '.escapeshellarg($domainsearch).' '.$domainchecker)) {
                echo "$domainsearch exists in file <br>";
                } else {
                echo "OK to proceed!";
                }

?>


