<?php
// check for board certification validity
date_default_timezone_set("America/Detroit");

function err($code, $text) {
	global $rVal;
	$rVal['error']['code']=$code;
	$rVal['error']['text']=$text;
	echo json_encode($rVal);
	exit($code);
}

function board_check($status, int $origyear, int $expyear) {
	// process board dates - return 0 if okay, 1 if hard fail, 2 if soft fail
	$rVal=array("code" => 0, "error" => "");
	$thisyear=date("Y");
	switch ($status) {
		case "R":
			if ($expyear===0) {$rVal['code']=1; $rVal['error']="No expiration year listed";}
			else if ($thisyear > $expyear) {$rVal['code']=1; $rVal['error']="Recert likely expired"; }
			else {$rVal['code']=2; $rVal['error']="Should this be 'M' or 'C' (participating in MOC/OCC)?";}
			return $rVal;
			break;
			
		case "O":
			if (($thisyear - $origyear) > 10) {$rVal['code']=1; $rVal['error']="Original cert possibly expired. Check if recertified or time-unlimited certification"; }
			return $rVal;
			break;
			
		case "L":
			$rVal['code']=2; $rVal['error']="Confirm lapsed certification (no longer certified)"; 
			return $rVal;
			break;

		case "N":
			if ($origyear > 2000) {$rVal['code']=1; $rVal['error']="Confirm time-unlimited certification"; }
			return $rVal;
			break;
			
		case "M":
		case "C":
            if (($expyear!=0) && ($expyear < $thisyear)) {$rVal['code']=1; $rVal['error']="Certification expired";}
			return $rVal;
			break;
			
		default:
			$rVal['code']=1; $rVal['error']="Processing Error"; 
			return $rVal;
			break;		
	}
}

$rVal=array(
	"error" => array (
		"code" => 0, 
		"text" => ""
	),
	"results" => array ()
);

// DEBUG - LOAD HTML *********************
//$_POST['boardblock']=file_get_contents("../samples/HFEM2019.html");

if ((!isset($_POST['boardblock'])) || (strlen($_POST['boardblock'])<10)) err(1, "Missing board text block");

// extract all info from boardblock
$dom=new DOMDocument();
@$dom->loadHTML($_POST['boardblock']);  // suppress error due to malformed HTML from ACGME
$xpath=new DOMXPath($dom);

$faculty=array();
$i=-1;
$rows=$xpath->query("//table[@id='tblRoster']/tbody/tr");
foreach ($rows as $row) {
    // the first cell in this table has a rowspan that indicates how many board certifications are under it
    $firstcell_rowspan=$row->firstChild->attributes->getNamedItem('rowspan');
    $cells=$row->childNodes;
    if ($firstcell_rowspan!==null) {
        // new faculty
        $i++;
        $faculty[$i]['name']=trim($cells->item(0)->childNodes->item(0)->nodeValue); // faculty name is a child node of 1st cell
        $faculty[$i]['certcount']=$firstcell_rowspan->nodeValue;
        $faculty[$i]['boards'][0]['specialty']=$cells->item(6)->nodeValue;
        $faculty[$i]['boards'][0]['boardname']=$cells->item(8)->nodeValue;
        $faculty[$i]['boards'][0]['origyear']=(int) $cells->item(10)->nodeValue;
        $faculty[$i]['boards'][0]['status']=$cells->item(12)->nodeValue;
        $faculty[$i]['boards'][0]['expyear']=(int) $cells->item(14)->nodeValue;
        $certcounter=0;
    }
    else {
        // additional certifications for the same faculty
        $certcounter++;
        $faculty[$i]['boards'][$certcounter]['specialty']=$cells->item(0)->nodeValue;
        $faculty[$i]['boards'][$certcounter]['boardname']=$cells->item(2)->nodeValue;
        $faculty[$i]['boards'][$certcounter]['origyear']=(int) $cells->item(4)->nodeValue;
        $faculty[$i]['boards'][$certcounter]['status']=$cells->item(6)->nodeValue;
        $faculty[$i]['boards'][$certcounter]['expyear']=(int) $cells->item(8)->nodeValue;
    }
}   
//print_r($faculty);

// check validity of data reported
foreach ($faculty as $fac) {
    foreach ($fac['boards'] as $board) {
        $check=board_check($board['status'], $board['origyear'], $board['expyear']);
        $r=array('name' => $fac['name'],
                 'specialty' => $board['specialty'],
                 'issues' => $check['code'],
                 'descr' => ($check['code']!=0) ? $check['error'] : null);
//        print_r($r);
        $rVal['results'][]=$r;
                 
    }
}

header('Content-Type: application/json');
echo json_encode($rVal, JSON_PRETTY_PRINT);
exit(0);
?>