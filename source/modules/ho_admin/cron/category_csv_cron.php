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

$_sThisImportConfig = oxRegistry::get("oxConfig");
$_sThisImportPath = $_sThisImportConfig->getConfigParam("HO_IMPORT_PATH");
$_sThisImportCSV = "../../../" . $_sThisImportPath . $_sThisImportConfig->getConfigParam("jCsvCategory");

$i = 0;

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
	
	fseek($jImportObject, $_REQUEST['seek']);
	
	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
	
		if($i >= 100) {
			header("LOCATION: ".$_SERVER['PHP_SELF']."?seek=".ftell($jImportObject)."");
		}
		
		$nCatIdQuery = "SELECT `oxid` from oxcategories where oxecoroid = '". $jImportData[1] ."';";
		$aCatIdQuery = "SELECT `oxid` from oxarticles where oxartnum = '".$jImportData[0]."' LIMIT 1;";
				
		$aCatid = oxDb::getDb()->getOne( $aCatIdQuery );
		$nCatid = oxDb::getDb()->getOne( $nCatIdQuery );

		$sQ = "INSERT INTO `jdcategory` (JDID, JDANUM, JDCATEGORY, JDECORO) VALUES ('','".$aCatid."','".$nCatid."', ".$jImportData[1].");";
		$sQuery = oxDb::getDb()->Execute( $sQ );
		
		$i++;
	}
	fclose($jImportObject);
	echo "Kategorien wurden in die Zuordnungstabelle geladen!";
}

/* CREATE TABLE `jdcategory` (
	`JDID` INT (5) AUTO_INCREMENT,
	`JDANUM` VARCHAR (128) NOT NULL DEFAULT '',
	`JDCATEGORY` VARCHAR (128) NOT NULL DEFAULT '',
	`JDECORO` INT (6),
	`JDTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
	PRIMARY KEY (`JDID`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8  */

?>