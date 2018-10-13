<?php
include(__DIR__."/../vendor/autoload.php");
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile($_FILES['adsfile']['tmp_name']);
 
$text = $pdf->getText();
echo $text;
?>