<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
 
ini_set('max_execution_time', 150);
 
class ho_export extends oxAdminView {
	/**
	 * Current class template name.
	 * @var string
	 */
	protected $_sThisTemplate = 'ho_export.tpl';
	
	public function render() {
		parent::render();
		// export to Ecoro
		if($_POST['exportEcoro']) {
			$csv = $this->createCSV($_POST['setEdit']);
			echo ($csv) ? "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #ddffb2;'>Bestellungen wurden exportiert!</div>" : "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #fcc4c4;'>Es sind keine neuen Bestellungen vorhanden oder es ist ein Fehler aufgetreten!</div>";
		}
		return $this->_sThisTemplate;
	}

	/**
     *
     * @return bool
     */
	
	static function deleteOrderCSV(){
		$_sThisExportConfig = oxRegistry::get("oxConfig");
		$_sThisExportPath = ho_export::getPathDelimiter() . $_sThisExportConfig->getConfigParam("HO_EXPORT_PATH");
		$_sThisExportFileHead = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_OCTO_ORDER");
		$_sThisExportFilePosition = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_ORDER_POSITION");
		
		$delHead = unlink($_sThisExportFileHead);
		$delPos = unlink($_sThisExportFilePosition);
		
		if($delHead && $delPos) {
			return true;
		}
		else {
			return false;
		}
    }

	static function getOrderName($id) {
		$sQ = "SELECT `OXBILLEMAIL` FROM `oxorder` where OXORDERNR = '". $id ."' LIMIT 1";
		return oxDb::getDb()->getOne( $sQ );
	}
	
	static function getOrderCountry($id) {
		$sQ = "SELECT `OXISOALPHA2` FROM `oxcountry` where OXID = '". $id ."' LIMIT 1";
		return oxDb::getDb()->getOne( $sQ );
	}
	
	static function getOrderShipping($id) {	
		$shippingArt = array('6.95'=>'401', '9.95'=>'402', '14.95'=>'403', '19.95'=>'404', '39.95'=>'405', '49.95'=>'406', '59.95'=>'407', '69.95'=>'408', 
							 '99.95'=>'409', '299.95'=>'410', '29.95'=>'411', '0'=>'412',  '79.95'=>'413', '89.95'=>'414', '119.95'=>'415', '129.95'=>'416', 
							 '149.95'=>'417', '159.95'=>'418', '179.95'=>'419', '199.95'=>'420', '249.95'=>'421', );
		return $shippingArt[$id];
	}

	static function getOrderPayment($id) {
		$paymentArt = array('oxidpaypal'=>'1', 'oxidcomfinanz'=>'102', '60be3bf6b6a443f7e197df1bf94c2ace'=>'104', 'oxidpayadvance'=>'201', 'billpay_rec'=>'301');
		return $paymentArt[$id];
	}
	
	static function setNameTitle($title) {
		return str_replace(array("MRS","MR"),array("Frau","Herr"),$title);
	}
	
	static function getOrderData() {
        $sQ = "SELECT o.oxid, o.oxordernr, o.oxorderdate, u.oxcustnr, o.oxremark, o.oxbillsal, o.oxbilllname, o.oxbillfname, o.oxbillstreet, o.oxbillstreetnr,
				o.oxbillzip, o.oxbillcity, o.oxbillfon, o.oxbillfax, o.oxbillcountryid, o.oxbillemail, u.oxbirthdate, o.oxdelsal, o.oxdelfname, o.oxdellname,
				o.oxdelstreet, o.oxdelstreetnr, o.oxdelzip, o.oxdelcity, o.oxdelfon, o.oxdelfax, o.oxdelcountryid, o.oxtotalordersum, o.oxcurrency, o.oxdelcost,
				o.oxpaymenttype, o.oxtransstatus, o.oxshopid, o.oxlang, o.oxpaycost, o.oxfolder FROM `oxorder` as o join `oxuser` as u ON o.oxuserid = u.oxid 
				where o.oxfolder = 'ORDERFOLDER_NEW' OR o.oxfolder = 'ORDERFOLDER_EBAY' ORDER BY o.oxordernr DESC"; 
        return oxDb::getDb()->Execute( $sQ );
	}
	
	static function getOrderDataTpl() {
		$rs = ho_export::getOrderData();
		$data .= "<tbody>";
       	while (!$rs->EOF) {
			$data .= "<tr style='border: 1px solid #cfcfcf; padding: 3px;'>
						<td>". $rs->fields[1] ."</td>
						<td></td>
						<td>". substr($rs->fields[2], 11) ."</td>
						<td>". $rs->fields[3] ."</td>
						<td>". $rs->fields[6] .", ". $rs->fields[7] ."</td>
						<td>". $rs->fields[27] ." &euro;</td>
						<td></td>
					</tr>";
			$rs->moveNext();
		}
		$data .= "</tbody>";
		
		return $data;
	}
	
	/**
     *
     * @return string
     */
	
	static function getOrderArticles($id) {
        $sQ = "SELECT o.oxordernr, a.oxartnum, a.oxamount, a.oxbprice, a.oxbrutprice, a.oxvat, o.oxcurrency, a.oxtitle, a.oxshortdesc, a.oxdelivery, a.oxbrutprice,
				a.oxlength, a.oxwidth, a.oxheight FROM `oxorderarticles` AS a JOIN `oxorder` AS o ON a.oxorderid = o.oxid WHERE a.oxorderid = '".$id."'"; 
        return oxDb::getDb()->Execute( $sQ );
	}
	
	/**
     *
     * @return string
     */
	
	static function getLogData() {
		$log = file_get_contents('/log/jumbo/order.log', true);
		return $log;
	}
	
	/**
     *
     * @return string
     */
	
	static function formatDate($date){
    	$year 	= substr($date,0,4);
    	$month 	= substr($date,5,2);
    	$day 	= substr($date,8,2);
    	return $year.$month.$day;
    }
	
	/**
     *
     * @return bool
     */
	
	static function csvData($file, $fields){
    	$fp = fopen($file ,'a+');
		
		foreach($fields as $array) {
			$cfields[] = mb_convert_encoding($array, 'ISO-8859-1', 'UTF-8');
		}
		
		$csv = @fputcsv($fp, $cfields, chr(9), chr(0));
		fclose($fp);
		return true;
    }
	
	static function getPathDelimiter() {
		if($_GET['cl'] == "ho_export") {
			return $_sThisPathDelimiter = "../";
		}
		else {
			return null;
		}
	}
	
	/**
     *
     * @return bool
     */
	 
	static function checkArtnum($artnum) {
		if($artnum == "") {
			return "17317.0.";
		}
		else {
			return $artnum;
		}
	}
	
	/**
     *
     * @return string
     */
	 
	static function getEbayName($str) {
		preg_match_all("/\((.*)\)/", $str, $treffer);  
		return $treffer[1][0];
	}
	
	
	/**
     *
     * @return string
     */
	 
	static function setTelephonNumber($tel) {
		$firstPos = substr($tel,0,1);
		$ec = str_replace(" ", "", $tel);
		$sc = str_replace("/", "", $ec);
		$bc = str_replace("-","",$sc);
		if($firstPos == "0") {
			$number = "+49" . substr($bc,1);
		}
		else {
			$number = $bc;
		}
		return $number;
	}

	/**
     *
     * @return void
     */
	
	static function createCSV($edit) {
		/**
		 *
		 */
		$_sThisExportConfig = oxRegistry::get("oxConfig");
		$_sThisExportPath = ho_export::getPathDelimiter() . $_sThisExportConfig->getConfigParam("HO_EXPORT_PATH");
		$_sThisExportFileHead = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_OCTO_ORDER");
		$_sThisExportFilePosition = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_ORDER_POSITION");
		/**
		 *
		 */	
		$rs = ho_export::getOrderData();
		/**
		 *
		 */
		while (!$rs->EOF) {
			$oxidID = $rs->fields[0];
			$Auftragsnummer = (ho_export::getShopID($rs->fields[0], $rs->fields[35], $rs->fields[36])).",".($rs->fields[1] + 14000); /* 1 */
			$Auftragsdatum = ho_export::formatDate(substr($rs->fields[2], 0, 10)); /* 2 */
			$Auftragszeit = substr($rs->fields[2], 11); /* 3 */
			$Kundennummer = $rs->fields[3]; /* 4 */
			$Lieferdatum  = ""; /* 6 */
			$Lieferstatus = ""; /* 7 */
			$Lieferhinweis = str_replace(array("\r\n", "\n", "\r"), ' ', $rs->fields[4]);/* 8 */
			$Anrede = ho_export::setNameTitle($rs->fields[5]); /* 9 */
			$Name1 = $rs->fields[6]; /* 10 */
			$Name2 = $rs->fields[7]; /* 11 */
			$Strasse = $rs->fields[8] ." ". $rs->fields[9]; /* 12 */
			$Postleitzahl = $rs->fields[10]; /* 13 */
			$Ort = $rs->fields[11]; /* 14 */
			$Telefon = ho_export::setTelephonNumber($rs->fields[12]); /* 15 */
			if(ho_export::getShopID($rs->fields[0], $rs->fields[35], $rs->fields[36]) == 82) {
				$Fax = ho_export::getEbayName($rs->fields[4]);
			}
			elseif (ho_export::getShopID($rs->fields[0], $rs->fields[35], $rs->fields[36]) == 88){
				$Fax = substr($rs->fields[4], 36);
			}
			elseif (ho_export::getShopID($rs->fields[0], $rs->fields[35], $rs->fields[36]) == 89){
				$Fax = substr($rs->fields[4], 36);
			}
			else {
				$Fax = $rs->fields[1];
			} /* $Fax = ($rs->fields[35] != 'ORDERFOLDER_EBAY') ? $rs->fields[1] : ho_export::getEbayName($rs->fields[4]); /* 16 */ 
			$Land = ho_export::getOrderCountry($rs->fields[14]); /* 17 */
			$Email = $rs->fields[15]; /* 18 */
			$Etage = ""; /* 19 */
			$Geburtsdatum = str_replace("-","",$rs->fields[16]); /* 20 */
			$Filiale = ho_export::getShopID($rs->fields[0], $rs->fields[35], $rs->fields[36]); /* 21 */
			$dAnrede = $rs->fields[17];
			$dName1 = $rs->fields[18];
			$dName2 = $rs->fields[19];
			$dStrasse = ($rs->fields[20] !== "") ?  $rs->fields[20] . " " . $rs->fields[21] : "";
			$dPostleitzahl = $rs->fields[22];
			$dOrt = $rs->fields[23];
			$dTelefon = ($rs->fields[24] == "") ? ho_export::setTelephonNumber($rs->fields[12]) : ho_export::setTelephonNumber($rs->fields[24]);
			$dFax = $rs->fields[25];
			$dLand = ho_export::getOrderCountry($rs->fields[26]);
			
			if( $Name2 == $dName2 && $Name1 == $dName1 && $Strasse == $dStrasse && $Postleitzahl == $dPostleitzahl && $Ort == $dOrt ) {
				$dAnrede = "";
				$dName1 = "";
				$dName2 = "";
				$dStrasse = "";
				$dPostleitzahl = "";
				$dOrt = "";
				$dTelefon = "";
				$dFax = "";
				$dLand = "";
			}
			
			$Gesamtpreis = $rs->fields[27]; /* 31 */
			$Waehrung = $rs->fields[28]; /* 32 */
			$Lieferart = ho_export::getOrderShipping($rs->fields[29]); /* 33 */
			$Lieferstatus = $rs->fields[29]; /* 34 */
			$Zahlungsart = ho_export::getOrderPayment($rs->fields[30]); /* 35 */
			$Zahlungsstatus = $rs->fields[34]; /* 36 */
			$Shopkennzeichen = $rs->fields[32]; /* 37 */
			$LieferstatusFremdsprache = $rs->fields[33]; /* 38 */
			$Handynummer = ""; /* 39 */
			$Teillieferung = ""; /* 40 */
			$Lieferstatusnumerisch = $rs->fields[29]; /* 41 */
			$Kopftour = ""; /* 42 */

				$fields = array( $Auftragsnummer /* 1 */, $Auftragsdatum /* 2 */, $Auftragszeit /* 3 */, $Kundennummer /* 4 */, $Anrede /* 5 */, $Name1 /* 6 */, $Name2 /* 7 */, 
								 $Strasse /* 8 */, $Postleitzahl /* 9 */, $Ort /* 10 */, $Telefon /* 11 */, $Fax /* 12 */, $Land /* 13 */, $Email /* 14 */, $Etage /* 15 */, 
								 $Geburtsdatum /* 16 */, $Filiale /* 17 */, $dAnrede /* 18 */, $dName1 /* 19 */, $dName2 /* 20 */, $dStrasse /* 21 */, $dPostleitzahl /* 22 */, 
								 $dOrt /* 23 */, $dTelefon /* 24 */, $dFax /* 25 */, $dLand /* 26 */, $Gesamtpreis /* 27 */, $Waehrung /* 28 */, $Lieferhinweis /* 29 */,$Lieferart /* 30 */, 
								 $Lieferstatus /* 31 */, $Zahlungsart /* 32 */, $Zahlungsstatus /* 33 */, $Shopkennzeichen /* 34 */, $LieferstatusFremdsprache /* 35 */, 
								 $Handynummer /* 36 */, $Teillieferung /* 37 */, $Lieferstatusnumerisch /* 38 */, $Kopftour /* 39 */ );
				
				$orderhead = ho_export::csvData($_sThisExportFileHead, $fields);
				
				$as = ho_export::getOrderArticles($rs->fields[0]);
				$PosNr = 1;
				while (!$as->EOF) {
					$PosID = $PosNr;
					$Artikelnummer = ho_export::checkArtnum($as->fields[1]);
					$Menge = $as->fields[2];
					$Einzelpreis = $as->fields[3];
					$Gesamtpreis = $as->fields[4];
					$MwSt = $as->fields[5];
					$Waehrung = $as->fields[6];
					$Artikelbezeichnung = $as->fields[7];
					$Artikelbeschreibung = $as->fields[8];
					$Lieferdatum  = $as->fields[9];
					$Lieferstatus = "";
					$LieferstatusText = "";
					$TrackingNr = "";
					$Lieferhinweis = "";
					$ZusatztextTeil = "";
					$Bruttopreis = $as->fields[10];
					$Nachlaesse = "";
					$Ausfuehrung1 = $as->fields[11];
					$Ausfuehrung2 = $as->fields[12];
					$Ausfuehrung3 = $as->fields[13];
					$FremdspracheZusatztext = "";
					$FremdspracheLieferstatus = "";
					$WEStatus = "";
					$lfdnrAuslieferung = "";
					$UhrzeitPTV = "";
					$vonUhrzeit = "";
					$bisUhrzeit = "";
					$ersterKunde = "";
					$Lieferart = "";

					$afields = array( $Auftragsnummer, $PosNr, $PosID, $Artikelnummer, $Menge, $Einzelpreis, $Gesamtpreis, $MwSt, $Waehrung, $Artikelbezeichnung, $Artikelbeschreibung, $Filiale, $Lieferdatum, $Lieferstatus, $LieferstatusText,
									  $TrackingNr, $Lieferhinweis, $ZusatztextTeil, $Bruttopreis,	$Nachlaesse, $Ausfuehrung1, $Ausfuehrung2, $Ausfuehrung3, $FremdspracheZusatztext, $FremdspracheLieferstatus, $WEStatus,
									  $lfdnrAuslieferung, $UhrzeitPTV, $vonUhrzeit, $bisUhrzeit, $ersterKunde, $Lieferart );
					@fputcsv($fp,$afields, chr(9));
						
					$orderposition = ho_export::csvData($_sThisExportFilePosition, $afields);
						
					$as->moveNext();
					$PosNr++;
				}
			ho_export::logOrder( 1, $rs->fields[1] );
			
			if($edit) {
				ho_export::setOrderEdit( $rs->fields[1] , $Filiale, $oxidID);
			}
			$rs->moveNext();
		}
		if($orderhead) {
			return true;
		}
	}
	
	/**
     *
     * @return string
     */
	
	static function setOrderEdit($id, $fil, $oxidID) {
		switch($fil) {
			case(80):
			$order = "ORDERFOLDER_FINISHED";
			break;
		
			case(82):
			$order = "ORDERFOLDER_EBAYFIN"; 
			break;
			
			case(88):
			$order = "ORDERFOLDER_PLUSFIN"; 
			break;
			
			case(89):
			$order = "ORDERFOLDER_AMAZONFIN"; 
			break;
			
			case(92):
			$order = "ORDERFOLDER_MYPACKFIN"; 
			break;
		}

		$sQ = "UPDATE oxorder set OXFOLDER = '".$order."' where OXID = '". $oxidID ."' LIMIT 1";

		$execute =  oxDb::getDb()->Execute( $sQ );
		
		if($execute) {
			ho_export::logOrder( 2, $id );
		}
		else {
			ho_export::logOrder( 0, $id );
		}
	}
	
	/**
     *
     * @return void
     */
	
	static function logOrder ( $error, $id ) {
		$handle = fopen(ho_export::getPathDelimiter() . "log/jumbo/order.log","a");
		switch($error) {
			case(0):
			$success = "Fehler: " . mysql_error() ."\n";
			break;
		
			case(1):
			$success = date("d.m.y H:i:s") . " - Bestellung " . $id . " (". ho_export::getOrderName($id) .") wurde exportiert\r\n"; 
			break;
			
			case(2):
			$success = date("d.m.y H:i:s") . " - Bestellung " . $id . " (". ho_export::getOrderName($id) .") wurde als erledigt gekennzeichnet\r\n"; 
			break;
		}
		fputs($handle, $success);
		fclose($handle);
	}
}
?>