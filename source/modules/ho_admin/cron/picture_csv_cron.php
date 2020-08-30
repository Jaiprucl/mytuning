<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript">
<!--
window.onbeforeunload = function(){
    window.opener.document.location.reload(true);
}
-->
</script>
<?php

require_once("../../../bootstrap.php");
$_sThisImportCSV = "../../../in/bilddaten.csv";

$i = 0;

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
	
	fseek($jImportObject, $_REQUEST['seek']);
	
	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
	
		if($i >= 100 ) {
			header("LOCATION: ".$_SERVER['PHP_SELF']."?seek=".ftell($jImportObject)."");
		}

		$image = str_replace('\\', '', substr($jImportData[1], strrpos($jImportData[1], '\\')));

		$sQ = "INSERT INTO `jdpictures` (JDID, JDANUM, JDSORT, JDITITLE) VALUES ('','".$jImportData[0]."','".$jImportData[2]."', '". $image ."');";
		$sQuery = oxDb::getDb()->Execute( $sQ );
		
		if(!$sQuery) {
			echo "Fehler : " . mysql_error() . "<br>";
		}
		
		$i++;
	}
	fclose($jImportObject);
	echo "Bilddaten wurden in die Zuordnungstabelle geladen!";
}

/* CREATE TABLE `jdpictures` (
	`JDID` INT (5) AUTO_INCREMENT,
	`JDANUM` VARCHAR (128) NOT NULL DEFAULT '',
	`JDITITLE` VARCHAR (255) NOT NULL DEFAULT '',
	`JDTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
	PRIMARY KEY (`JDID`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 

ALTER TABLE  `jdpictures` ADD  `JDSORT` INT( 2 ) NULL COMMENT  'Sortierungsnummer für Ordner' AFTER  `JDANUM` ;
*/

?>