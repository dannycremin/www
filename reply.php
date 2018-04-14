<!DOCTYPE html>
<html>
<head>
<style>
table {
    border-collapse: collapse;
}
table, th, td {
    border: 1px solid black;
}
</style>
</head>
<body>

<?php

// Check if the entered domain is .bit or a regular TLD - dig based on the result.
// If user enters www. on record strip it.
// If user enters .bit on query strip it

$prestringlower= $_POST["queryinput"];
// make the string lowercase
$queryentry= $prestringlower;
$queryentry= strtolower($prestringlower);

if (strpos($queryentry, '.bit') !== false) {
	
	// If user enters www. on record strip it and pass to $querystripwww variable
		
	$querystripwww= str_replace("www.", "", $queryentry);
	echo "<b>1. If the query contains www. remove it</b>" ."<br><br>";
	echo "$querystripwww" . "<br><br>";


		
	// If user enters .bit on query strip it out and pass to $dotbitquery variable
	
	
	$dotbitquery= str_replace(".bit", "", $querystripwww); 
	

	
	echo "<b>2. Query the Blockchain for $dotbitquery</b>";
	echo "<br><br>";
	
	$dotbitqueryresult= shell_exec("sudo /usr/bin/namecoind name_show d/$dotbitquery 2>&1");
	echo "<pre>$dotbitqueryresult</pre>";
	
	
	$json= $dotbitqueryresult;
	$decodedjson= json_decode($json);
	$dotbitip= str_replace("\"", "", $decodedjson->value);
	$dotbitdns = "$dotbitquery.bit";
	
	// Output the IP address pulled from blockchain
	
	echo "<b>3. Decode JSON for the 'value' field and display IP</b>";
	
	echo "<br><br>";
	
	echo $dotbitip;
	
	echo "<br><br>";
	
	// Output the FQDN from the blockchain
	
	echo "<b>4. Display the Fully Qualified Domain Name (FDQN) from the Blockchain</b>";
	
	echo "<br><br>";
	
	echo $dotbitdns;
	
	echo "<br><br>";

// Open SQL connection to add SOA record based on .bit query but check if it already exists first.
	
// Output the successful addition of the SOA record
	
	echo "<b>5. Create a Start/Source of Authority (SOA) for the query in PowerDNS</b>";
	
	echo "<br><br>";
	
	include "/var/databasecreds.php";	
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}
	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', 'admin@$dotbitdns','SOA',86400,NULL) AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='SOA')";	

	if (mysqli_query($conn, $sql)) {
    echo "Added SOA record for <i>$dotbitdns</i>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	
	echo "<br><br>";
	


// Open a 2nd SQL connection to add A record based on .bit query but check if it already exists first.	

	// Output the successful addition of the SOA record
	
	echo "<b>6. Create an A record for the query in PowerDNS</b>";
	
	echo "<br><br>";

	include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', '$dotbitdns', '$dotbitip','A',86400,NULL)
	AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='$dotbitdns' AND type='A')";

	if (mysqli_query($conn, $sql)) {
    echo "Added A record for <i>$dotbitdns</i>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	
	echo "<br><br>";

// Open a 3nd SQL connection to add A record to add www. to the beginning of the newly added .bit A record entry.

	// Output the successful addition of the www.record.bit output.
	echo "<b>7. Create an A record for the query with www.</b>" . "<br><br>";

	include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO records (domain_id, name, content, type, ttl, prio) SELECT * FROM (SELECT '2', 'www.$dotbitdns', '$dotbitip','A',86400,NULL)
	AS tmp WHERE NOT EXISTS (SELECT * FROM records WHERE name='www.$dotbitdns' AND type='A')";

	if (mysqli_query($conn, $sql)) {
    echo "Added A record for www." . "<i>$dotbitdns</i>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	echo "<br><br>";	


// Run pdnssec on the created zone to rectify-zone

	$rectifyzoneoutput = shell_exec("sudo /usr/bin/pdnssec rectify-zone $dotbitdns 2>&1");

	

// Final - dig out the .bit query from pdns

	$dotbitfinaldig = shell_exec("dig $queryentry @127.0.0.1 -p 54 2>&1");
	echo "<b>8. Query reponse from PowerDNS via Blockchain </b>";
	echo "<br>";
	echo "<pre>$dotbitfinaldig</pre>";
	
} else {
	
// If its a TLD, then we should check if the domain is valid against IANA list - then proceed with dig	
	
$text = $queryentry;
if (preg_match('%\b(.AAA|.AARP|.ABARTH|.ABB|.ABBOTT|.ABBVIE|.ABC|.ABLE|.ABOGADO|.ABUDHABI|.AC|.ACADEMY|.ACCENTURE|.ACCOUNTANT|.ACCOUNTANTS|.ACO|.ACTIVE|.ACTOR|.AD|.ADAC|.ADS|.ADULT|.AE|.AEG|.AERO|.AETNA|.AF|.AFAMILYCOMPANY|.AFL|.AFRICA|.AG|.AGAKHAN|.AGENCY|.AI|.AIG|.AIGO|.AIRBUS|.AIRFORCE|.AIRTEL|.AKDN|.AL|.ALFAROMEO|.ALIBABA|.ALIPAY|.ALLFINANZ|.ALLSTATE|.ALLY|.ALSACE|.ALSTOM|.AM|.AMERICANEXPRESS|.AMERICANFAMILY|.AMEX|.AMFAM|.AMICA|.AMSTERDAM|.ANALYTICS|.ANDROID|.ANQUAN|.ANZ|.AO|.AOL|.APARTMENTS|.APP|.APPLE|.AQ|.AQUARELLE|.AR|.ARAB|.ARAMCO|.ARCHI|.ARMY|.ARPA|.ART|.ARTE|.AS|.ASDA|.ASIA|.ASSOCIATES|.AT|.ATHLETA|.ATTORNEY|.AU|.AUCTION|.AUDI|.AUDIBLE|.AUDIO|.AUSPOST|.AUTHOR|.AUTO|.AUTOS|.AVIANCA|.AW|.AWS|.AX|.AXA|.AZ|.AZURE|.BA|.BABY|.BAIDU|.BANAMEX|.BANANAREPUBLIC|.BAND|.BANK|.BAR|.BARCELONA|.BARCLAYCARD|.BARCLAYS|.BAREFOOT|.BARGAINS|.BASEBALL|.BASKETBALL|.BAUHAUS|.BAYERN|.BB|.BBC|.BBT|.BBVA|.BCG|.BCN|.BD|.BE|.BEATS|.BEAUTY|.BEER|.BENTLEY|.BERLIN|.BEST|.BESTBUY|.BET|.BF|.BG|.BH|.BHARTI|.BI|.BIBLE|.BID|.BIKE|.BING|.BINGO|.BIO|.BIZ|.BJ|.BLACK|.BLACKFRIDAY|.BLANCO|.BLOCKBUSTER|.BLOG|.BLOOMBERG|.BLUE|.BM|.BMS|.BMW|.BN|.BNL|.BNPPARIBAS|.BO|.BOATS|.BOEHRINGER|.BOFA|.BOM|.BOND|.BOO|.BOOK|.BOOKING|.BOSCH|.BOSTIK|.BOSTON|.BOT|.BOUTIQUE|.BOX|.BR|.BRADESCO|.BRIDGESTONE|.BROADWAY|.BROKER|.BROTHER|.BRUSSELS|.BS|.BT|.BUDAPEST|.BUGATTI|.BUILD|.BUILDERS|.BUSINESS|.BUY|.BUZZ|.BV|.BW|.BY|.BZ|.BZH|.CA|.CAB|.CAFE|.CAL|.CALL|.CALVINKLEIN|.CAM|.CAMERA|.CAMP|.CANCERRESEARCH|.CANON|.CAPETOWN|.CAPITAL|.CAPITALONE|.CAR|.CARAVAN|.CARDS|.CARE|.CAREER|.CAREERS|.CARS|.CARTIER|.CASA|.CASE|.CASEIH|.CASH|.CASINO|.CAT|.CATERING|.CATHOLIC|.CBA|.CBN|.CBRE|.CBS|.CC|.CD|.CEB|.CENTER|.CEO|.CERN|.CF|.CFA|.CFD|.CG|.CH|.CHANEL|.CHANNEL|.CHASE|.CHAT|.CHEAP|.CHINTAI|.CHRISTMAS|.CHROME|.CHRYSLER|.CHURCH|.CI|.CIPRIANI|.CIRCLE|.CISCO|.CITADEL|.CITI|.CITIC|.CITY|.CITYEATS|.CK|.CL|.CLAIMS|.CLEANING|.CLICK|.CLINIC|.CLINIQUE|.CLOTHING|.CLOUD|.CLUB|.CLUBMED|.CM|.CN|.CO|.COACH|.CODES|.COFFEE|.COLLEGE|.COLOGNE|.COM|.COMCAST|.COMMBANK|.COMMUNITY|.COMPANY|.COMPARE|.COMPUTER|.COMSEC|.CONDOS|.CONSTRUCTION|.CONSULTING|.CONTACT|.CONTRACTORS|.COOKING|.COOKINGCHANNEL|.COOL|.COOP|.CORSICA|.COUNTRY|.COUPON|.COUPONS|.COURSES|.CR|.CREDIT|.CREDITCARD|.CREDITUNION|.CRICKET|.CROWN|.CRS|.CRUISE|.CRUISES|.CSC|.CU|.CUISINELLA|.CV|.CW|.CX|.CY|.CYMRU|.CYOU|.CZ|.DABUR|.DAD|.DANCE|.DATA|.DATE|.DATING|.DATSUN|.DAY|.DCLK|.DDS|.DE|.DEAL|.DEALER|.DEALS|.DEGREE|.DELIVERY|.DELL|.DELOITTE|.DELTA|.DEMOCRAT|.DENTAL|.DENTIST|.DESI|.DESIGN|.DEV|.DHL|.DIAMONDS|.DIET|.DIGITAL|.DIRECT|.DIRECTORY|.DISCOUNT|.DISCOVER|.DISH|.DIY|.DJ|.DK|.DM|.DNP|.DO|.DOCS|.DOCTOR|.DODGE|.DOG|.DOHA|.DOMAINS|.DOT|.DOWNLOAD|.DRIVE|.DTV|.DUBAI|.DUCK|.DUNLOP|.DUNS|.DUPONT|.DURBAN|.DVAG|.DVR|.DZ|.EARTH|.EAT|.EC|.ECO|.EDEKA|.EDU|.EDUCATION|.EE|.EG|.EMAIL|.EMERCK|.ENERGY|.ENGINEER|.ENGINEERING|.ENTERPRISES|.EPOST|.EPSON|.EQUIPMENT|.ER|.ERICSSON|.ERNI|.ES|.ESQ|.ESTATE|.ESURANCE|.ET|.ETISALAT|.EU|.EUROVISION|.EUS|.EVENTS|.EVERBANK|.EXCHANGE|.EXPERT|.EXPOSED|.EXPRESS|.EXTRASPACE|.FAGE|.FAIL|.FAIRWINDS|.FAITH|.FAMILY|.FAN|.FANS|.FARM|.FARMERS|.FASHION|.FAST|.FEDEX|.FEEDBACK|.FERRARI|.FERRERO|.FI|.FIAT|.FIDELITY|.FIDO|.FILM|.FINAL|.FINANCE|.FINANCIAL|.FIRE|.FIRESTONE|.FIRMDALE|.FISH|.FISHING|.FIT|.FITNESS|.FJ|.FK|.FLICKR|.FLIGHTS|.FLIR|.FLORIST|.FLOWERS|.FLY|.FM|.FO|.FOO|.FOOD|.FOODNETWORK|.FOOTBALL|.FORD|.FOREX|.FORSALE|.FORUM|.FOUNDATION|.FOX|.FR|.FREE|.FRESENIUS|.FRL|.FROGANS|.FRONTDOOR|.FRONTIER|.FTR|.FUJITSU|.FUJIXEROX|.FUN|.FUND|.FURNITURE|.FUTBOL|.FYI|.GA|.GAL|.GALLERY|.GALLO|.GALLUP|.GAME|.GAMES|.GAP|.GARDEN|.GB|.GBIZ|.GD|.GDN|.GE|.GEA|.GENT|.GENTING|.GEORGE|.GF|.GG|.GGEE|.GH|.GI|.GIFT|.GIFTS|.GIVES|.GIVING|.GL|.GLADE|.GLASS|.GLE|.GLOBAL|.GLOBO|.GM|.GMAIL|.GMBH|.GMO|.GMX|.GN|.GODADDY|.GOLD|.GOLDPOINT|.GOLF|.GOO|.GOODHANDS|.GOODYEAR|.GOOG|.GOOGLE|.GOP|.GOT|.GOV|.GP|.GQ|.GR|.GRAINGER|.GRAPHICS|.GRATIS|.GREEN|.GRIPE|.GROCERY|.GROUP|.GS|.GT|.GU|.GUARDIAN|.GUCCI|.GUGE|.GUIDE|.GUITARS|.GURU|.GW|.GY|.HAIR|.HAMBURG|.HANGOUT|.HAUS|.HBO|.HDFC|.HDFCBANK|.HEALTH|.HEALTHCARE|.HELP|.HELSINKI|.HERE|.HERMES|.HGTV|.HIPHOP|.HISAMITSU|.HITACHI|.HIV|.HK|.HKT|.HM|.HN|.HOCKEY|.HOLDINGS|.HOLIDAY|.HOMEDEPOT|.HOMEGOODS|.HOMES|.HOMESENSE|.HONDA|.HONEYWELL|.HORSE|.HOSPITAL|.HOST|.HOSTING|.HOT|.HOTELES|.HOTELS|.HOTMAIL|.HOUSE|.HOW|.HR|.HSBC|.HT|.HU|.HUGHES|.HYATT|.HYUNDAI|.IBM|.ICBC|.ICE|.ICU|.ID|.IE|.IEEE|.IFM|.IKANO|.IL|.IM|.IMAMAT|.IMDB|.IMMO|.IMMOBILIEN|.IN|.INDUSTRIES|.INFINITI|.INFO|.ING|.INK|.INSTITUTE|.INSURANCE|.INSURE|.INT|.INTEL|.INTERNATIONAL|.INTUIT|.INVESTMENTS|.IO|.IPIRANGA|.IQ|.IR|.IRISH|.IS|.ISELECT|.ISMAILI|.IST|.ISTANBUL|.IT|.ITAU|.ITV|.IVECO|.IWC|.JAGUAR|.JAVA|.JCB|.JCP|.JE|.JEEP|.JETZT|.JEWELRY|.JIO|.JLC|.JLL|.JM|.JMP|.JNJ|.JO|.JOBS|.JOBURG|.JOT|.JOY|.JP|.JPMORGAN|.JPRS|.JUEGOS|.JUNIPER|.KAUFEN|.KDDI|.KE|.KERRYHOTELS|.KERRYLOGISTICS|.KERRYPROPERTIES|.KFH|.KG|.KH|.KI|.KIA|.KIM|.KINDER|.KINDLE|.KITCHEN|.KIWI|.KM|.KN|.KOELN|.KOMATSU|.KOSHER|.KP|.KPMG|.KPN|.KR|.KRD|.KRED|.KUOKGROUP|.KW|.KY|.KYOTO|.KZ|.LA|.LACAIXA|.LADBROKES|.LAMBORGHINI|.LAMER|.LANCASTER|.LANCIA|.LANCOME|.LAND|.LANDROVER|.LANXESS|.LASALLE|.LAT|.LATINO|.LATROBE|.LAW|.LAWYER|.LB|.LC|.LDS|.LEASE|.LECLERC|.LEFRAK|.LEGAL|.LEGO|.LEXUS|.LGBT|.LI|.LIAISON|.LIDL|.LIFE|.LIFEINSURANCE|.LIFESTYLE|.LIGHTING|.LIKE|.LILLY|.LIMITED|.LIMO|.LINCOLN|.LINDE|.LINK|.LIPSY|.LIVE|.LIVING|.LIXIL|.LK|.LLC|.LOAN|.LOANS|.LOCKER|.LOCUS|.LOFT|.LOL|.LONDON|.LOTTE|.LOTTO|.LOVE|.LPL|.LPLFINANCIAL|.LR|.LS|.LT|.LTD|.LTDA|.LU|.LUNDBECK|.LUPIN|.LUXE|.LUXURY|.LV|.LY|.MA|.MACYS|.MADRID|.MAIF|.MAISON|.MAKEUP|.MAN|.MANAGEMENT|.MANGO|.MAP|.MARKET|.MARKETING|.MARKETS|.MARRIOTT|.MARSHALLS|.MASERATI|.MATTEL|.MBA|.MC|.MCKINSEY|.MD|.ME|.MED|.MEDIA|.MEET|.MELBOURNE|.MEME|.MEMORIAL|.MEN|.MENU|.MEO|.MERCKMSD|.METLIFE|.MG|.MH|.MIAMI|.MICROSOFT|.MIL|.MINI|.MINT|.MIT|.MITSUBISHI|.MK|.ML|.MLB|.MLS|.MM|.MMA|.MN|.MO|.MOBI|.MOBILE|.MOBILY|.MODA|.MOE|.MOI|.MOM|.MONASH|.MONEY|.MONSTER|.MOPAR|.MORMON|.MORTGAGE|.MOSCOW|.MOTO|.MOTORCYCLES|.MOV|.MOVIE|.MOVISTAR|.MP|.MQ|.MR|.MS|.MSD|.MT|.MTN|.MTR|.MU|.MUSEUM|.MUTUAL|.MV|.MW|.MX|.MY|.MZ|.NA|.NAB|.NADEX|.NAGOYA|.NAME|.NATIONWIDE|.NATURA|.NAVY|.NBA|.NC|.NE|.NEC|.NET|.NETBANK|.NETFLIX|.NETWORK|.NEUSTAR|.NEW|.NEWHOLLAND|.NEWS|.NEXT|.NEXTDIRECT|.NEXUS|.NF|.NFL|.NG|.NGO|.NHK|.NI|.NICO|.NIKE|.NIKON|.NINJA|.NISSAN|.NISSAY|.NL|.NO|.NOKIA|.NORTHWESTERNMUTUAL|.NORTON|.NOW|.NOWRUZ|.NOWTV|.NP|.NR|.NRA|.NRW|.NTT|.NU|.NYC|.NZ|.OBI|.OBSERVER|.OFF|.OFFICE|.OKINAWA|.OLAYAN|.OLAYANGROUP|.OLDNAVY|.OLLO|.OM|.OMEGA|.ONE|.ONG|.ONL|.ONLINE|.ONYOURSIDE|.OOO|.OPEN|.ORACLE|.ORANGE|.ORG|.ORGANIC|.ORIGINS|.OSAKA|.OTSUKA|.OTT|.OVH|.PA|.PAGE|.PANASONIC|.PANERAI|.PARIS|.PARS|.PARTNERS|.PARTS|.PARTY|.PASSAGENS|.PAY|.PCCW|.PE|.PET|.PF|.PFIZER|.PG|.PH|.PHARMACY|.PHD|.PHILIPS|.PHONE|.PHOTO|.PHOTOGRAPHY|.PHOTOS|.PHYSIO|.PIAGET|.PICS|.PICTET|.PICTURES|.PID|.PIN|.PING|.PINK|.PIONEER|.PIZZA|.PK|.PL|.PLACE|.PLAY|.PLAYSTATION|.PLUMBING|.PLUS|.PM|.PN|.PNC|.POHL|.POKER|.POLITIE|.PORN|.POST|.PR|.PRAMERICA|.PRAXI|.PRESS|.PRIME|.PRO|.PROD|.PRODUCTIONS|.PROF|.PROGRESSIVE|.PROMO|.PROPERTIES|.PROPERTY|.PROTECTION|.PRU|.PRUDENTIAL|.PS|.PT|.PUB|.PW|.PWC|.PY|.QA|.QPON|.QUEBEC|.QUEST|.QVC|.RACING|.RADIO|.RAID|.RE|.READ|.REALESTATE|.REALTOR|.REALTY|.RECIPES|.RED|.REDSTONE|.REDUMBRELLA|.REHAB|.REISE|.REISEN|.REIT|.RELIANCE|.REN|.RENT|.RENTALS|.REPAIR|.REPORT|.REPUBLICAN|.REST|.RESTAURANT|.REVIEW|.REVIEWS|.REXROTH|.RICH|.RICHARDLI|.RICOH|.RIGHTATHOME|.RIL|.RIO|.RIP|.RMIT|.RO|.ROCHER|.ROCKS|.RODEO|.ROGERS|.ROOM|.RS|.RSVP|.RU|.RUGBY|.RUHR|.RUN|.RW|.RWE|.RYUKYU|.SA|.SAARLAND|.SAFE|.SAFETY|.SAKURA|.SALE|.SALON|.SAMSCLUB|.SAMSUNG|.SANDVIK|.SANDVIKCOROMANT|.SANOFI|.SAP|.SAPO|.SARL|.SAS|.SAVE|.SAXO|.SB|.SBI|.SBS|.SC|.SCA|.SCB|.SCHAEFFLER|.SCHMIDT|.SCHOLARSHIPS|.SCHOOL|.SCHULE|.SCHWARZ|.SCIENCE|.SCJOHNSON|.SCOR|.SCOT|.SD|.SE|.SEARCH|.SEAT|.SECURE|.SECURITY|.SEEK|.SELECT|.SENER|.SERVICES|.SES|.SEVEN|.SEW|.SEX|.SEXY|.SFR|.SG|.SH|.SHANGRILA|.SHARP|.SHAW|.SHELL|.SHIA|.SHIKSHA|.SHOES|.SHOP|.SHOPPING|.SHOUJI|.SHOW|.SHOWTIME|.SHRIRAM|.SI|.SILK|.SINA|.SINGLES|.SITE|.SJ|.SK|.SKI|.SKIN|.SKY|.SKYPE|.SL|.SLING|.SM|.SMART|.SMILE|.SN|.SNCF|.SO|.SOCCER|.SOCIAL|.SOFTBANK|.SOFTWARE|.SOHU|.SOLAR|.SOLUTIONS|.SONG|.SONY|.SOY|.SPACE|.SPIEGEL|.SPORT|.SPOT|.SPREADBETTING|.SR|.SRL|.SRT|.ST|.STADA|.STAPLES|.STAR|.STARHUB|.STATEBANK|.STATEFARM|.STATOIL|.STC|.STCGROUP|.STOCKHOLM|.STORAGE|.STORE|.STREAM|.STUDIO|.STUDY|.STYLE|.SU|.SUCKS|.SUPPLIES|.SUPPLY|.SUPPORT|.SURF|.SURGERY|.SUZUKI|.SV|.SWATCH|.SWIFTCOVER|.SWISS|.SX|.SY|.SYDNEY|.SYMANTEC|.SYSTEMS|.SZ|.TAB|.TAIPEI|.TALK|.TAOBAO|.TARGET|.TATAMOTORS|.TATAR|.TATTOO|.TAX|.TAXI|.TC|.TCI|.TD|.TDK|.TEAM|.TECH|.TECHNOLOGY|.TEL|.TELECITY|.TELEFONICA|.TEMASEK|.TENNIS|.TEVA|.TF|.TG|.TH|.THD|.THEATER|.THEATRE|.TIAA|.TICKETS|.TIENDA|.TIFFANY|.TIPS|.TIRES|.TIROL|.TJ|.TJMAXX|.TJX|.TK|.TKMAXX|.TL|.TM|.TMALL|.TN|.TO|.TODAY|.TOKYO|.TOOLS|.TOP|.TORAY|.TOSHIBA|.TOTAL|.TOURS|.TOWN|.TOYOTA|.TOYS|.TR|.TRADE|.TRADING|.TRAINING|.TRAVEL|.TRAVELCHANNEL|.TRAVELERS|.TRAVELERSINSURANCE|.TRUST|.TRV|.TT|.TUBE|.TUI|.TUNES|.TUSHU|.TV|.TVS|.TW|.TZ|.UA|.UBANK|.UBS|.UCONNECT|.UG|.UK|.UNICOM|.UNIVERSITY|.UNO|.UOL|.UPS|.US|.UY|.UZ|.VA|.VACATIONS|.VANA|.VANGUARD|.VC|.VE|.VEGAS|.VENTURES|.VERISIGN|.VERSICHERUNG|.VET|.VG|.VI|.VIAJES|.VIDEO|.VIG|.VIKING|.VILLAS|.VIN|.VIP|.VIRGIN|.VISA|.VISION|.VISTA|.VISTAPRINT|.VIVA|.VIVO|.VLAANDEREN|.VN|.VODKA|.VOLKSWAGEN|.VOLVO|.VOTE|.VOTING|.VOTO|.VOYAGE|.VU|.VUELOS|.WALES|.WALMART|.WALTER|.WANG|.WANGGOU|.WARMAN|.WATCH|.WATCHES|.WEATHER|.WEATHERCHANNEL|.WEBCAM|.WEBER|.WEBSITE|.WED|.WEDDING|.WEIBO|.WEIR|.WF|.WHOSWHO|.WIEN|.WIKI|.WILLIAMHILL|.WIN|.WINDOWS|.WINE|.WINNERS|.WME|.WOLTERSKLUWER|.WOODSIDE|.WORK|.WORKS|.WORLD|.WOW|.WS|.WTC|.WTF|.XBOX|.XEROX|.XFINITY|.XIHUAN|.XIN|.XN--11B4C3D|.XN--1CK2E1B|.XN--1QQW23A|.XN--2SCRJ9C|.XN--30RR7Y|.XN--3BST00M|.XN--3DS443G|.XN--3E0B707E|.XN--3HCRJ9C|.XN--3OQ18VL8PN36A|.XN--3PXU8K|.XN--42C2D9A|.XN--45BR5CYL|.XN--45BRJ9C|.XN--45Q11C|.XN--4GBRIM|.XN--54B7FTA0CC|.XN--55QW42G|.XN--55QX5D|.XN--5SU34J936BGSG|.XN--5TZM5G|.XN--6FRZ82G|.XN--6QQ986B3XL|.XN--80ADXHKS|.XN--80AO21A|.XN--80AQECDR1A|.XN--80ASEHDB|.XN--80ASWG|.XN--8Y0A063A|.XN--90A3AC|.XN--90AE|.XN--90AIS|.XN--9DBQ2A|.XN--9ET52U|.XN--9KRT00A|.XN--B4W605FERD|.XN--BCK1B9A5DRE4C|.XN--C1AVG|.XN--C2BR7G|.XN--CCK2B3B|.XN--CG4BKI|.XN--CLCHC0EA0B2G2A9GCD|.XN--CZR694B|.XN--CZRS0T|.XN--CZRU2D|.XN--D1ACJ3B|.XN--D1ALF|.XN--E1A4C|.XN--ECKVDTC9D|.XN--EFVY88H|.XN--ESTV75G|.XN--FCT429K|.XN--FHBEI|.XN--FIQ228C5HS|.XN--FIQ64B|.XN--FIQS8S|.XN--FIQZ9S|.XN--FJQ720A|.XN--FLW351E|.XN--FPCRJ9C3D|.XN--FZC2C9E2C|.XN--FZYS8D69UVGM|.XN--G2XX48C|.XN--GCKR3F0F|.XN--GECRJ9C|.XN--GK3AT1E|.XN--H2BREG3EVE|.XN--H2BRJ9C|.XN--H2BRJ9C8C|.XN--HXT814E|.XN--I1B6B1A6A2E|.XN--IMR513N|.XN--IO0A7I|.XN--J1AEF|.XN--J1AMH|.XN--J6W193G|.XN--JLQ61U9W7B|.XN--JVR189M|.XN--KCRX77D1X4A|.XN--KPRW13D|.XN--KPRY57D|.XN--KPU716F|.XN--KPUT3I|.XN--L1ACC|.XN--LGBBAT1AD8J|.XN--MGB9AWBF|.XN--MGBA3A3EJT|.XN--MGBA3A4F16A|.XN--MGBA7C0BBN0A|.XN--MGBAAKC7DVF|.XN--MGBAAM7A8H|.XN--MGBAB2BD|.XN--MGBAI9AZGQP6J|.XN--MGBAYH7GPA|.XN--MGBB9FBPOB|.XN--MGBBH1A|.XN--MGBBH1A71E|.XN--MGBC0A9AZCG|.XN--MGBCA7DZDO|.XN--MGBERP4A5D4AR|.XN--MGBGU82A|.XN--MGBI4ECEXP|.XN--MGBPL2FH|.XN--MGBT3DHD|.XN--MGBTX2B|.XN--MGBX4CD0AB|.XN--MIX891F|.XN--MK1BU44C|.XN--MXTQ1M|.XN--NGBC5AZD|.XN--NGBE9E0A|.XN--NGBRX|.XN--NODE|.XN--NQV7F|.XN--NQV7FS00EMA|.XN--NYQY26A|.XN--O3CW4H|.XN--OGBPF8FL|.XN--OTU796D|.XN--P1ACF|.XN--P1AI|.XN--PBT977C|.XN--PGBS0DH|.XN--PSSY2U|.XN--Q9JYB4C|.XN--QCKA1PMC|.XN--QXAM|.XN--RHQV96G|.XN--ROVU88B|.XN--RVC1E0AM3E|.XN--S9BRJ9C|.XN--SES554G|.XN--T60B56A|.XN--TCKWE|.XN--TIQ49XQYJ|.XN--UNUP4Y|.XN--VERMGENSBERATER-CTB|.XN--VERMGENSBERATUNG-PWB|.XN--VHQUV|.XN--VUQ861B|.XN--W4R85EL8FHU5DNRA|.XN--W4RS40L|.XN--WGBH1C|.XN--WGBL6A|.XN--XHQ521B|.XN--XKC2AL3HYE2A|.XN--XKC2DL3A5EE0H|.XN--Y9A3AQ|.XN--YFRO4I67O|.XN--YGBI2AMMX|.XN--ZFR164B|.XPERIA|.XXX|.XYZ|.YACHTS|.YAHOO|.YAMAXUN|.YANDEX|.YE|.YODOBASHI|.YOGA|.YOKOHAMA|.YOU|.YOUTUBE|.YT|.YUN|.ZA|.ZAPPOS|.ZARA|.ZERO|.ZIP|.ZIPPO|.ZM|.ZONE|.ZUERICH|.ZW)\b%i', $text) > 0) {
    
//echo if a top level domain was matched
    $tldqueryresult = shell_exec("dig   $text  2>&1");
	echo "<pre>$tldqueryresult</pre>";
	
} else {
    echo "You entered a non-valid domain";
}
	
//	$tldqueryresult = shell_exec("dig   $queryentry  2>&1");
//	echo "<h3>DNS information for $queryentry</h3>";
//	echo  "<br>";
//	echo "<pre>$tldqueryresult</pre>";	
}


//Code to remove records from database if they are .bit entries

echo "<b>9. Delete the queries from PowerDNS database</b>" . "<br><br>";

include "/var/databasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "DELETE from records where domain_id = 2";

	if (mysqli_query($conn, $sql)) {
    echo "Removed all records for <i>$dotbitdns</i> from database" . "<br><br>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);

// Log remote IP address & query.
echo "<b>10. Log the remote IP and query</b>" . "<br><br>";

$remoteipaddress = $_SERVER['REMOTE_ADDR'];

	
include "/var/iplogdatabasecreds.php";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO iplog (RemoteIP, Query) VALUES ('$remoteipaddress', '$queryentry')";

	if (mysqli_query($conn, $sql)) {
    echo "Added <i>$remoteipaddress</i> and <i>$dotbitdns</i> to IP log database" . "<br><br>";
	} else {
    echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);

echo "<b>11. Display last 10 queries, IP and times</b>" . "<br><br>";

include "/var/iplogdatabasecreds.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT  RemoteIP, Query, Time from iplog ORDER BY Time DESC LIMIT 10";
// $sql = "SELECT RemoteIP, Query, Time FROM iplog";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
		echo "<table><tr><th>Remote IP</th><th>Query</th><th>Time</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
		echo "<tr><td>" . $row["RemoteIP"]. "</td><td>" . $row["Query"]. "</td><td>" . $row["Time"]. "</td></tr>";
    }
} else {
    echo "0 results";
}
$conn->close();


?>	
	



