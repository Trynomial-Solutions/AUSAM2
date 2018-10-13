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

function board_check($type, $init, $recert) {
	// process board dates - return 0 if okay, 1 if hard fail, 2 if soft fail
	$rVal=array("code" => 0, "error" => "");
	$thisyear=date("Y");
	switch ($type) {
		case "R":
			if (($recert+0)===0) {$rVal['code']=1; $rVal['error']="No recert year listed";}
			else if (($thisyear - $recert) > 10) {$rVal['code']=1; $rVal['error']="Recert possibly expired"; }
			else {$rVal['code']=2; $rVal['error']="Should this be 'M' or 'C' (participating in MOC/OCC)?";}
			return $rVal;
			break;
			
		case "O":
			if (($thisyear - $init) > 10) {$rVal['code']=1; $rVal['error']="Original cert possibly expired. Check if recertified or time-unlimited certification"; }
			return $rVal;
			break;
			
		case "L":
			$rVal['code']=1; $rVal['error']="Certification lapsed"; 
			return $rVal;
			break;

		case "N":
			if ($init > 2000) {$rVal['code']=1; $rVal['error']="Confirm time-unlimited certification"; }
			return $rVal;
			break;
			
		case "M":
		case "C":
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

$_POST['boardblock']= <<<BB
Physician Faculty Roster

List alphabetically and by site all physician faculty who have a significant role (teaching or mentoring) in the education of residents/fellows and who have documented qualifications to instruct and supervise. List the Program Director first.

Core physician faculty must:
 Devote sufficient time to the educational program to fulfill their supervisory and teaching responsibilities and demonstrate a strong interest in resident education
 Administer and maintain an educational environment conducive to educating residents in each of the ACGME competency areas
 Participate in faculty development programs designed to enhance the effectiveness of their teaching and to promote scholarly activity
 Establish and maintain an environment of inquiry and scholarship with an active research component
 Regularly participate in organized clinical discussions, rounds, journal clubs, and conferences
 Encourage and support residents in pursuing scholarly activities
 Be clinically active
 Devote the majority of their professional efforts to the program
All physicians who devote at least 15 hours per week to resident education and administration are designated as core faculty.

All core physician faculty should teach and advise residents as well as participate in at least 1 of the following:
 Evaluate the competency domains
 Work closely with and support the program director
 Assist in developing and implementing evaluation systems
Program directors will not be designated as core faculty.

Continued Accreditation programs: A CV is only required for the program director.

New Applications and Initial Accreditation programs: A CV is required for the program director and each active physician faculty member on your roster.


Name	Core Faculty	Based Mainly at Inst. #	Specialties / Certifications	No. of Years Teaching in This Specialty	Average Hours Per Week Spent On
Specialty / Certification	Cert	Original Cert Year	Cert Status	Re-cert Year	Clinical Supervision	Admin	Didactic Teaching	Research
Nikhil Goyal, MD
(Program Director, Transitional Year Residency Program)	N	1	Emergency medicine	ABMS	2007	R	2018	9	20	15	9	1
Clinical informatics	ABMS	2016	O	--
Internal medicine	ABMS	2006	M	2016
Odaliz Abreu-Lanfranco, MD
(Program Director, Internal Medicine Residency)	Y	1	Internal medicine	ABMS	2006	M	2016	1	10	27	6	1
Infectious disease	ABMS	2012	N	--
Bradley Jaskulka, MD
(Associate Program Director, Transitional Year Residency)	Y	1	Emergency medicine	ABMS	2008	M	--	3	20	10	2	1
Sports medicine	ABMS	2009	M	--
Vinay Shah, MD
(Internal Medicine Faculty, CCC & PEC Member)	Y	1	Internal medicine	ABMS	1997	M	2017	5	20	1	1	1
Taher Vohra, MD
(Emergency Medicine Residency Program Director)	Y	1	Emergency medicine	ABMS	2006	M	2015	4	20	1	2	1
Certification Status:
Certification in the primary specialty refers to Board Certification. Certification for the secondary specialty refers to sub-board certification. If the secondary specialty is a core ACGME specialty (e.g., Internal Medicine, Pediatrics, etc.), the certification question refers to Board Certification.

R = Re-Certified
O = Time Limited Certificate/Original Certification Currently Valid
L = Certification Lapsed
N = Time-unlimited certificate/no Re-Certification
M = Meets MOC/CC Requirements
C = Meets Osteopathic Continuous Certification (OCC)
Based Mainly at Institution #:
1 = [250331] Henry Ford Hospital
*=Institution is an elective rotation site.
**=Institution not on list of active participating sites.
Educational Focus:
† = Program and Osteopathic Faculty
† † = Osteopathic Faculty
BB;

if ((!isset($_POST['boardblock'])) || (strlen($_POST['boardblock'])<10)) err(1, "Missing board text block");

// extract all info from boardblock
preg_match_all('/(([\w -]+), .*[\r\n]+\D+\d{1,2}\t)?([\w ]+)\t(ABMS|AOA)\s+(\d{4})\s+([ROLNMC])\s+(--|\d{4})/m', $_POST['boardblock'], $matches, PREG_SET_ORDER);

$boardcerts=array();
foreach($matches as $match) {
	if ($match[2]!='') {
		// new faculty
		$bc=array();
		$bc['name']=$match[2];
	}
	$bc['specialty']=$match[4]." ".$match[3];
	$bc['orig_year']=$match[5];
	$bc['status']=$match[6];
	if ($match[7]==='--') $match[7]=null;
	$bc['recert_year']=$match[7];
	$boardcerts[]=$bc;
}

// verify board certifications
foreach ($boardcerts as $bc) {
	$r=array();
	$check=board_check($bc['status'], $bc['orig_year'], $bc['recert_year']);
	$r['name']=$bc['name'];
	$r['specialty']=$bc['specialty'];
	$r['issues']=$check['code'];
	if ($check['code']!==0) $r['descr']=$check['error'];
	$rVal['results'][]=$r;
}

header('Content-Type: application/json');
echo json_encode($rVal, JSON_PRETTY_PRINT);
exit(0);
?>