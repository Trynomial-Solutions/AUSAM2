<?php
include(__DIR__."/../vendor/autoload.php");
$parser = new \Smalot\PdfParser\Parser();
if (isset($_GET['testfile'])) $pdf=$parser->parseFile('../samples/'.$_GET['testfile'].'.pdf');
else $pdf    = $parser->parseFile($_FILES['adsfile']['tmp_name']);
 
$text = $pdf->getText();
echo $text;
?>