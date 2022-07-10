<?php
/*******************************
vars passed by POST (becoz long query string)
	ay = academic year ('2013' for 2013-14)
	pmidblock = block of text with embedded PMIDs

Returns - json 

error{code, text}
	0 if successful, text blank
	1 if CURL error, text is actual error
	2 preg_match failed
	3 no PMIDs read
	4 ay or pmids not sent or invalid
	
pmid_count = # of PMIDs read
pmid_dated = # of PMIDs with dates read

pmids = array with 
	date_found = 1 or 0 if a date was successfulyl read
	date_type = epubdate or pubdate or entrezdate (if only year in pubdate)
	date_valid = 1 or 0 publication was first seen in the academic year
	date = formatted date
	date_as_read = date as read from PMID record
	authorstr = comma separated author list
	title = title of publication

********************************/


function chkdate($check, $ay) {
	// convert text date to timestamp, check if it is within the specificed academic year
	$year=substr($check,0,4);
	$month=substr($check,5,3);
	$day=substr($check, 9);
	if ($day=="" || !is_numeric($day)) $day="01";
	else if (strlen($day)==1) $day="0".$day;		// to fix quirk on strtotime when 2013-Jul-3 was being read as June 30 2013
	
//	echo ":$year:$month:$day";
	$date=strtotime($year."-".$month."-".$day);
	if ($date===false) return array(-1, $check);
	
//	echo date(":Y-m-d", $date).":".$ay;
	$txtdate=date("M j Y", $date);
	
	if ($year<$ay) return array(0, $txtdate);
	if ($year==$ay) {
		if (date("n", $date)<7) return array(0, $txtdate);
		else return array(1, $txtdate);
	}
	if ($year==$ay+1) {
		if (date('n', $date)>6) return array(0, $txtdate);
		else return array(1, $txtdate);
	}
	if ($year>$ay+1) return array(0, $txtdate);
}

function authorstr($pub) {
	$authorstr=array();
	foreach ($pub['authors'] as $auth) $authorstr[]=$auth['name'];
	return implode(", ", $authorstr);
}

function err($code, $text) {
	global $rVal;
	$rVal['error']['code']=$code;
	$rVal['error']['text']=$text;
	echo json_encode($rVal);
	exit($code);
}

$rVal=array(
	"error" => array (
		"code" => 0, 
		"text" => ""
	),
	"pmid_count" => 0,
	"pmid_dated" => 0,
	"pmids" => array ()
);

$pmid_invalid=$pmid_oor=0;

date_default_timezone_set("America/Detroit");
if (!isset($_POST['debug'])) $_POST['debug']=0;
if (isset($_POST['ay'])) $ay=$_POST['ay']; else err(4, "ay not defined");
if (!is_numeric($ay)) err(4, "Invalid AY");
if (isset($_POST['pmidblock'])) $pmidblock=$_POST['pmidblock']; else err(4, "pmidblock not defined");
if (strlen($pmidblock)<8) err(4, "invalid PMID block");

$matches=array();
$count=preg_match_all("/\b\d{8}\b/", $pmidblock, $matches);
if ($count===false) err (2, "preg_match_all error");
if ($count===0) err (3, "No PMIDs found");
$rVal['pmid_count']=$count;

$url="https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&retmode=json&version=2.0&id=".implode(',',$matches[0]);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
$json = curl_exec($ch);
if($json===false) err(1, curl_error($ch));
curl_close($ch);

$result=json_decode($json, true);
//echo $json;
$data=$result['result'];
if ($_POST['debug']==1) $rVal['debug']=$data;

foreach ($data['uids'] as $uid) {
	$lookingat="";
	
	// use epubdate if available, otherwise pubdate
	if (isset($data[$uid]['epubdate']) && $data[$uid]['epubdate']!='') $lookingat="epubdate";
	else if (isset($data[$uid]['pubdate']) && $data[$uid]['pubdate']!='') {
//		if (!is_numeric(substr($data[$uid]['pubdate'],5,3))) {
		if (strtotime(substr($data[$uid]['pubdate'],5,3))===false) {
			// pubdate format like "2015 Winter"
			$data[$uid]['pubdate']=substr($data[$uid]['pubdate'],0,4);
		}
		$lookingat="pubdate";
	}
	else {
		// no date found in returned data
		$rVal['pmids'][$uid]['date_found']=0;
		continue;
	}

	if (strlen($data[$uid][$lookingat])<5) {
		// only year noted in pubdate/epubdate - use entrez date instead
		for ($i=0; $i<count($data[$uid]["history"]); $i++) {
			if ($data[$uid]["history"][$i]["pubstatus"]=="entrez") {
				$chkdate=$data[$uid]["history"][$i]["date"];
				$dateval=strtotime($chkdate);
				$chkdate=date("Y M d", $dateval);
//				echo $chkdate;
				$lookingat="entrezdate";
			}
		}
	}	
	else $chkdate=$data[$uid][$lookingat];

	$rVal['pmids'][$uid]['date_type']=$lookingat;
	$rVal['pmids'][$uid]['date_as_read']=$chkdate;
	$check=chkdate($chkdate, $ay);
	if ($check[0]===-1) {
		$rVal['pmids'][$uid]['date_found']=0;
		$pmid_invalid++;
	}
	else $rVal['pmids'][$uid]['date_found']=1;

	$rVal['pmids'][$uid]['date_valid']=$check[0];
	if ($check[0]===0) $pmid_oor++;
	$rVal['pmids'][$uid]['date']=$check[1];
	$rVal['pmids'][$uid]['authorstr']=authorstr($data[$uid]);
	$rVal['pmids'][$uid]['title']=$data[$uid]['title'];
	$rVal['pmid_dated']++;
}

/*
// write statistics
require_once("../inc/web_connecti.inc.php");
$sql="INSERT INTO ausam_stats (pmid_count, pmid_dated, pmid_oor, pmid_invalid) VALUES (?, ?, ?, ?)";
$stmt=$web_dbi->prepare($sql);
$stmt->bind_param("iiii", $rVal['pmid_count'], $rVal['pmid_dated'], $pmid_oor, $pmid_invalid);
$stmt->execute();
$stmt->close();
*/
header('Content-Type: application/json');
echo json_encode($rVal);
//print_r($rVal);
exit(0);
?>