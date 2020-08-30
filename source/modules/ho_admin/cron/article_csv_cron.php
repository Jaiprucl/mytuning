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
$_sThisImportCSV = "../../../in/artikeldaten.csv";

$i = 0;

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
	
	fseek($jImportObject, $_REQUEST['seek']);
	
	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
	
		if($i >= 100) {
			header("LOCATION: ".$_SERVER['PHP_SELF']."?seek=".ftell($jImportObject)."");
		}
		
		/* $jImportCount = count($jImportData); */
			
		$insert = array(
			'oxid' => oxUtilsObject::getInstance()->generateUID(),
			'oxartnum' => $jImportData[0],
			'oxtitle' => utf8_encode($jImportData[1]),
			'oxvat' => $jImportData[3]*100,
			'oxprice' => $jImportData[10],
			'oxactive' => $jImportData[13],
			'oxissearch' => $jImportData[14],
			'oxheight' => $jImportData[7],
			'oxwidth' => $jImportData[8],
			'oxlength' => $jImportData[9],
			'oxcategory' => utf8_encode($jImportData[16]),
			'oxlongdesc' => utf8_encode($jImportData[4]),
			'oxweight' => utf8_encode($jImportData[17]),
			'oxean' => utf8_encode($jImportData[28]),
		);
		
		$sQ = "INSERT INTO `jdarticles` (";
			
		foreach( array_keys($insert) as $k ) { 
			$sQ .= "`" . $k ."`";
			if(end(@array_keys($insert)) != $k) {
				$sQ .= ", ";
			}
		}
			
		$sQ .= ") VALUES (";
			
		foreach( $insert as $k ) { 
			$sQ .= "'" . $k ."'";
			if(end($insert) != $k) {
				$sQ .= ", ";
			}
		} 
			
		$sQ .= ");";
			
		oxDb::getDb()->Execute( $sQ );
			
		$i++;
	}
	fclose($jImportObject);
	echo "Artikeldaten wurden in die Zuordnungstabelle geladen!";
}
?>