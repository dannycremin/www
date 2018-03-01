<?php

// Check in dannytabletest1 if the name Mikey exists, if it doesn't insert it. 

INSERT INTO dannytabletest1 (id, name) SELECT * FROM (SELECT '4','Bob') AS tmp WHERE NOT EXISTS ( SELECT * FROM dannytabletest1 WHERE name="Bob" );

// Check in records for dannycremin.bit if it doesn't exist add it of type SOA.

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'dannycremin.bit', 'localhost localhost 1','SOA',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="dannycremin.bit" AND type="SOA");

// Check in records for dannycremin.bit if it doesn't exist add it of type A and IP address 50.40.20.30

INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'dannycremin.bit', '50.40.20.30','A',86400,NULL)
AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name="dannycremin.bit" AND type="A");

?>