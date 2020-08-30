<?php
require_once("../../../bootstrap.php");
$_sThisImportCSV = "../../../in/shopkategorie.csv";

if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

	while (($jImportData = fgetcsv($jImportObject, 3000, chr(9))) !== FALSE) {
		echo $jImportData[0] . " " . $jImportData[2] ." - ";
		if($jImportData[2] != "") {
			$pCatId = "SELECT `oxid` from oxcategories where oxecoroid = '". $jImportData[2] ."' LIMIT 1";
			$pCat = oxDb::getDb()->getOne( $pCatId );
			echo "Parentnummer: " . $pCat . " (";
			echo $pCatId . ") ";
			if($pCat == "") {
				$sCatId = "SELECT oxid from oxcategories where oxtitle = '". utf8_encode($jImportData[1]) ."' AND OXPARENTID != 'oxrootid';";
			}
			else {
				$sCatId = "SELECT oxid from oxcategories where oxtitle = '". utf8_encode($jImportData[1]) ."' AND OXPARENTID = '".$pCat."';";
			}
		}
		else {
			$sCatId = "SELECT oxid from oxcategories where oxtitle = '". utf8_encode($jImportData[1]) ."' AND OXPARENTID = 'oxrootid';";
		}
		
		echo "PCat: ".$pCat."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .$sCatId . " ";
		
		$nCatid = oxDb::getDb()->getOne( $sCatId );
		/* echo $nCatid . " "; */
		if($nCatid != "") {		
			$sQ = "UPDATE `oxcategories` SET `oxecoroid` = ".$jImportData[0]." where `oxid` = '".$nCatid."';";
			oxDb::getDb()->Execute( $sQ );
			/* echo $sQ; */
		}
		else {
			// Main Kategory finden
			$sQ = "SELECT `oxid` from `oxcategories` WHERE `oxecoroid` = '". $jImportData[2] ."';";
			$jMainCat = oxDb::getDb()->getOne( $sQ );
			
			if($jMainCat == "") {
				$jMainCat = "oxrootid";
			}
			
			// neue Kategorie anlegen
			/* $oCategory = oxNew("oxcategory");
			$oCategory->assign(array(
			"OXECOROID" => $jImportData[0],
			"OXACTIVE_1" => 1,
			"OXTITLE" => $jImportData[1],
			"OXPRICEFROM" => 0,
			"OXPRICETO" => 0,
			"OXPARENTID" => $jMainCat
			));
			$oCategory->oxcategories__oxtitle1 = new oxField($category->group_name);
			$oCategory->oxcategories__oxtitle2 = new oxField($category->group_name_uk);
			$oCategory->save(); */
		}
		echo "<br>";
	}
	fclose($jImportObject);
}

/* ALTER TABLE  `oxcategories` ADD  `OXECOROID` INT( 6 ) NULL COMMENT  'Ecoro Kategorie ID' AFTER  `OXID` ; */
?>