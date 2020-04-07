<?php

require_once("../../../bootstrap.php");
$_sThisImportCSV = "../../../in/lieferarten.csv";

$i = 0;

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
	
	fseek($jImportObject, $_REQUEST['seek']);
	
	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
	
		if($i >= 100) {
			header("LOCATION: ".$_SERVER['PHP_SELF']."?seek=".ftell($jImportObject)."");
		}
		
		
			
		$i++;
	}
	fclose($jImportObject);
}

	/* ALTER TABLE  `oxdelivery` ADD  `OXECOROID` INT( 5 ) NULL COMMENT  'Ecoro Versand ID' AFTER  `OXID`; */

?>