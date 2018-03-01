<?php

// Check in dannytabletest1 if the name Mikey exists, if it doesn't insert it. 

INSERT INTO dannytabletest1 (id, name) SELECT * FROM (SELECT '4','Bob') AS tmp WHERE NOT EXISTS ( SELECT * FROM dannytabletest1 WHERE name="Bob" );

// Check in records for dannycremin.bit if it doesn't exist add it of type SOA.

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'dannycremin.bit', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="dannycremin.bit" AND type="SOA");

// Check in records for dannycremin.bit if it doesn't exist add it of type A and IP address 50.40.20.30

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'dannycremin.bit', '50.40.20.30','A',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="dannycremin.bit" AND type="A");


// Added the ability to take user input for SOA record and name.

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbiweboutput', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="$dotbitweboutput" AND type="SOA");

// Added ability to take user input for A record, name and IP.

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitweboutput', '$ipoutput','A',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="$dotbitweboutput" AND type="A");




$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio)
VALUES (2,'$dotbitweboutput','localhost localhost 1','SOA',86400,NULL),
	   (2,'$dotbitweboutput','$ipoutput','A',120,NULL)";

?>