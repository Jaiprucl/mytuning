<?php

require_once("../../../bootstrap.php");

$sQ = "SELECT * FROM `oxobject2category`;";
$rs = oxDb::getDb()->Execute( $sQ );

while (!$rs->EOF) {
	
	$sQ = "SELECT `oxparentid` FROM `oxcategories` WHERE `oxid` = '". $rs->fields[2] ."' LIMIT 1";
	$sParentID = oxDb::getDb()->getOne( $sQ );
	
	$time = ($sParentID == "oxrootid") ? 10 : 0;
	
	if($sParentID != "") {
		$sQ = "UPDATE `oxobject2category` SET `oxtime` = ".$time."  WHERE `oxid` = '". $rs->fields[0] ."' LIMIT 1";
		$sQuery = oxDb::getDb()->getOne( $sQ );
	}
	else {
		$sQ = "DELETE FROM `oxobject2category` WHERE `oxid` = '". $rs->fields[0] ."';";
		$sQuery = oxDb::getDb()->getOne( $sQ );
		echo $sQ . "<br>";
	}	
	
	$rs->moveNext();
}

echo "Fertig";
?>