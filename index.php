<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
body { font-family: Calibri; font-size: 10pt; color: #333333; } 
div.thecontent 	{ border:2px black solid; padding-left:10px; padding-right:10px; margin-left:10px; margin-right:10px; background-color:white; }
div.thedoc		{ border:2px #bababa solid; padding:10px; margin:10px; background-color:cacaca; }
</style>
</head>

<body>
<h1>Crawler för digital anslagstavla.</h1>

<?php



//VARs
$hasJQUERY = 0;		// jQuery

$hasGA = 0; 		// Google Analytics
$hasKISS = 0;		// Kissmetrics
$hasSC = 0;			// Site Catalyst
$hasGTM = 0;		// Google Tag Manager
					// Mixpanel : cdn.mxpnl.com/cache/db8adb02ec3f193cc89dd55eec5bdf7b/bundle/universal.min.js
					// SITE IMPROVE : //se1.siteimprove.com/js/siteanalyze_7228.js
					// piwik/piwik.js
					
$CMS = "";			// Sitevision : /sitevision/4.0.3-41/portlet/sitevision-portlets-min.css 
					

	if (PHP_SAPI == "cli") $lb = "\n";
	else $lb = "<br />";


// It may take a whils to crawl a site ...
set_time_limit(40000);

// Inculde the phpcrawl-mainclass
include("../_resources/PHPCrawl_083/libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
  function handleDocumentInfo($DocInfo) 
  {
	global $hasGA;	global $hasKISS;	global $hasJQUERY;	global $hasSC;	global $hasGTM; global $CMS;
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";
	echo "<div class=thecontent>".$lb;
    echo "<li>Page requested: <b>".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb."</b>";
    echo "<li>Referer-page: ".$DocInfo->referer_url.$lb;
    if ($DocInfo->received == true) {
		
		// TESTER
		if( strpos($DocInfo->content,"ga.js") > 0 ) 				{ $hasGA = 1; }
		if( strpos($DocInfo->content,"www.google-analytics.com/analytics.js") > 0 ) 				{ $hasGA = 1; }
		if( strpos($DocInfo->content,"GoogleAnalyticsObject") > 0 ) { $hasGA = 1; }
		if( strpos($DocInfo->content,"google-analytics") > 0 ) 		{ $hasGA = 1; }
		if( strpos($DocInfo->content,"_getTracker") > 0 ) 			{ $hasGA = 1; }

		
		if( strpos($DocInfo->content,"kissmetrics") > 0 ) 			{ $hasKISS = 1; }
		if( strpos($DocInfo->content,"jquery") > 0 ) 				{ $hasJQUERY = 1; }
		if( strpos($DocInfo->content,"siteCatalystAccount") > 0 ) 	{ $hasSC = 1; }
		
		if( strpos($DocInfo->content,"GoogleTagManager") > 0 ) 		{ $hasGTM = 1; }	
		if( strpos($DocInfo->content,"gtm.js") > 0 ) 				{ $hasGTM = 1; }

		if( strpos($DocInfo->content,"sites/default/files") > 0 ) 	{ $CMS = "drupal"; }
		if( strpos($DocInfo->content,"drupal") > 0 ) 				{ $CMS = "drupal"; }

		if( strpos($DocInfo->content,"EPi.") > 0 ) 					{ $CMS = "Episerver"; }
		if( strpos($DocInfo->content,"EPiServer") > 0 ) 			{ $CMS = "Episerver"; }
		if( strpos($DocInfo->content,"sitevision") > 0 ) 			{ $CMS = "sitevision"; }
		if( strpos($DocInfo->content,"wordpress") > 0 ) 			{ $CMS = "wordpress"; }

		
		
		// SKRIV UT
//		if($hasJQUERY) 	echo "<li>JQUERY på POS : ".strpos($DocInfo->content,"jquery.")."</h2>".$lb;
//		if($hasGA) 		echo "<li>GA på POS : ".strpos($DocInfo->content,"ga.js")."</h2>".$lb;
//		if($hasSC) 		echo "<li>SiteCatalyst på POS : ".strpos($DocInfo->content,"siteCatalystAccount")."</h2>".$lb;	
//		if($hasKISS)	echo "<li>kissmetrics på POS : ".strpos($DocInfo->content,"kissmetrics")."</h2>".$lb;	
//		if($hasGTM) 	echo "<li>GTM på POS : ".strpos($DocInfo->content,"GoogleTagManager")."</h2>".$lb;
		
		echo "<li>Content received: ".$DocInfo->bytes_received." bytes</h3>".$lb;
//		echo "<pre>".$lb;
//		echo htmlentities($DocInfo->content).$lb;
//		echo "</pre>".$lb;
	
	}else {
		echo "<li>Content not received".$lb; 
	}
    echo $lb;
	echo "</div>".$lb;
    flush();
  } 
}


// UPPRÄTTA DATABAS
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "d_dashboard";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 

$theFromDate = date("Y-m-d H:i:s"); $theFromDate = date("Y-m-d H:i:s", strtotime($theFromDate . ' -1 day'));
//$sql = "SELECT * FROM organisation WHERE datestamp < '" . $theFromDate . "' LIMIT 100";
//$sql = "SELECT * FROM organisation WHERE hasGA=0 AND hasSC=0 AND hasKISS=0 AND hasGTM=0";
$sql = "SELECT * FROM organisation WHERE CMS=''";

$result = $conn->query($sql);




// LOOPAR IGENOM RESULTATET
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

	$theID 		= $row["id"];
	$theURL 	= $row["url"];
	$theNAME 	= $row["name"];

	// #1 UPPRÄTTA ETT DOK
	echo "<div class=thedoc>".$lb; 

	// NOLLSTÄLL VARS
	$hasGA = 0;
	$hasKISS = 0;
	$hasJQUERY = 0;
	$hasSC = 0;
	$hasGTM = 0;
	$CMS = "";

	// STARTA CRAWLER 
	$crawler = new MyCrawler();
	$crawler->addContentTypeReceiveRule("#text/html#");
	$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
	$crawler->enableCookieHandling(true);
	$crawler->setTrafficLimit(2000 * 1024);
	$crawler->setPageLimit(5);
	$crawler->setURL($theURL);
	$crawler->go();

	
	// SPARA RESULTATET
	
	$sql = "UPDATE organisation SET CMS='".$CMS."', datestamp = now(), hasGA = ".$hasGA.", hasKISS = ".$hasKISS.", hasGTM = ".$hasGTM.", hasJQUERY = ".$hasJQUERY.", hasSC = ".$hasSC." WHERE ID=".$theID;

	if ($conn->query($sql) === TRUE) {
    	echo "<b>" . $theNAME . " uppdaterad!</b><br>";
		echo "SQL : " . $sql . "<br><br>";
	} else {
    	echo "Error: " . $sql . "<br>" . $conn->error;
	}


	
	// SKITA UR SLUTRAPPORT OCH AVSLUTA DOK
	$report = $crawler->getProcessReport();
	
		
	echo "Summary:".$lb;
	echo "Links followed: ".$report->links_followed.$lb;
	echo "Documents received: ".$report->files_received.$lb;
	echo "Bytes received: ".$report->bytes_received." bytes".$lb;
	echo "Process runtime: ".$report->process_runtime." sec".$lb; 
	echo "</div>".$lb; 	
	


    }
}


// STÄNG DATABAS
$conn->close();
?>

</body>
</html>
