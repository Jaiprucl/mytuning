<?php

require_once("../../../bootstrap.php");
$_sThisImportCSV = "../../../in/signets.csv";

$i = 0;

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
	
	fseek($jImportObject, $_REQUEST['seek']);
	
	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
	
		if($i >= 100) {
			header("LOCATION: ".$_SERVER['PHP_SELF']."?seek=".ftell($jImportObject)."");
		}
		
		$jImportDataString = explode(" ", $jImportData[1]);
		
		if(is_numeric($jImportDataString[0])) {
			$sQcheck = "SELECT `jdid` FROM `jdsignets` WHERE `jdid` = ".$jImportData[0]."";
			$sCheck = oxDb::getDb()->getOne( $sQcheck );
				if(empty($sCheck)) {
					$sQ = "INSERT INTO `jdsignets` (jdid, jddesc) VALUES ('". $jImportData[0] ."','". $jImportDataString[0] ."');";
					$sDB = oxDb::getDb()->Execute( $sQ );
				}
				else {
					$sQ = "UPDATE `jdsignets` SET `jddesc` = '".$jImportDataString[0]."' WHERE `jdid` = ".$jImportData[0]."";
					$sDB = oxDb::getDb()->Execute( $sQ );
				}
				
				if(!$sDB) {
					echo "Fehler: " . mysql_error() . "<br>";
				}
		}
		
		$i++;
	}
	fclose($jImportObject);
}

/* CREATE TABLE `jdsignets` (
	`JDID` INT (5) NOT NULL,
	`JDDESC` VARCHAR (255) NOT NULL DEFAULT '',
	`JDTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
	PRIMARY KEY (`JDID`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8  */
?>