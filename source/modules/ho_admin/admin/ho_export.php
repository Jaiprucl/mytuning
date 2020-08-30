<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
 
class ho_export extends oxAdminView {
	/**
	 * Current class template name.
	 * @var string
	 */
	protected $_sThisTemplate = 'ho_export.tpl';
	
	public function render() {
		parent::render();
		if($_REQUEST['export-octoflex']) {
			$this->createCSV();
		}
		return $this->_sThisTemplate;
	}

	/**
     *
     * @return string
     */

	public function getOrderList() {
		$query = "SELECT o.oxordernr, DATE_FORMAT(o.oxorderdate, '%d.%m.%Y') AS oxorderdate, (SELECT `oxcustnr` FROM oxuser WHERE `oxid` = o.oxuserid) as oxuserid, 
				o.oxbillsal, o.oxbilllname, o.oxbillfname, o.oxbillstreet, o.oxbillstreetnr, o.oxbillzip, o.oxbillcity, 
				(SELECT `oxtitle` FROM `oxcountry` WHERE `oxid` = o.oxbillcountryid) AS oxbillcountry, CONCAT(o.oxtotalordersum, ' ', o.oxcurrency) AS oxtotal, o.oxdelcost
				FROM `oxorder` as o
				where o.oxfolder = 'ORDERFOLDER_NEW' ORDER BY o.oxordernr DESC"; 

		$resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
		//Fetch all at once (beware of big arrays)
		$allResults = $resultSet->fetchAll();
		$_sThisRow = array();
		foreach($allResults as $row) {
				//do something
				$_sThisRow[] = $row;
		}
		return $_sThisRow;
	}

	static function createCSV($edit=0) {
	
		/**
		 */

		$_sThisExportConfig = oxRegistry::get("oxConfig");
		$_sThisExportPath =  getShopBasePath() . $_sThisExportConfig->getConfigParam("HO_EXPORT_PATH") . $_sThisExportConfig->getConfigParam("HO_ORDER_PATH");
	
		/**
		 */	

		$sQ = "SELECT o.oxid, o.oxordernr, DATE_FORMAT(o.oxorderdate, '%d.%m.%Y') AS oxorderdate, (SELECT `oxcustnr` FROM oxuser WHERE `oxid` = o.oxuserid) as oxuserid, o.oxdelfname, o.oxdellname,
		o.oxdelstreet, o.oxdelstreetnr, o.oxdelzip, o.oxdelcity, (SELECT `oxtitle` FROM `oxcountry` WHERE `oxid` = o.oxdelcountryid) AS oxdelcountry, o.oxtotalordersum, o.oxdelcost,
		(SELECT `oxdesc` FROM `oxpayments` WHERE `oxid` = o.oxpaymenttype) as oxpayment, a.oxartnum, a.oxamount, a.oxbprice, a.oxbrutprice, a.oxtitle
		FROM `oxorder` as o left join `oxorderarticles` as a ON a.oxorderid = o.oxid 
		where o.oxfolder = 'ORDERFOLDER_NEW' ORDER BY o.oxordernr DESC"; 
		
		$resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sQ);
		//Fetch all at once (beware of big arrays)
		$allResults = $resultSet->fetchAll();

		$_sThisRowHeadArray = array('oxordernr','oxorderdate','oxuserid','oxdelfname','oxdellname','oxdelstreet','oxdelstreetnr','oxdelzip','oxdelcity','oxdelcountry',
											'oxtotalordersum','oxdelcost','oxpayment','oxartnum','oxamount','oxbprice','oxbrutprice','oxtitle');
		$_sThisRowArray = array();

		foreach($allResults as $row) {
			$_allRows = count($row);
			$_sThisRowArray[] = [
				'oxordernr' => $row[1],
				'oxorderdate' => $row[2],
				'oxuserid' => $row[3],
				'oxdelfname' => $row[4],
				'oxdellname' => $row[5],
				'oxdelstreet' => $row[6],
				'oxdelstreetnr' => $row[7],
				'oxdelzip' => $row[8],
				'oxdelcity' => $row[9],
				'oxdelcountry' => $row[10],
				'oxtotalordersum' => $row[11],
				'oxdelcost' => $row[12],
				'oxpayment' => $row[13],
				'oxartnum' => $row[14],
				'oxamount' => $row[15],
				'oxbprice' => $row[16],
				'oxbrutprice' => $row[17],
				'oxtitle' => $row[18]
			];	
			ho_export::setOrderEdit($row[0]);
			ho_export::logOrder( 1, $row[1] );
		}	
		ho_export::csvData($_sThisExportPath . "order_" . $row[1] .".csv", $_sThisRowHeadArray, $_sThisRowArray);
			
		/* if($edit) {
			ho_export::setOrderEdit( $rs->fields[1] , $Filiale, $oxidID);
		} */
		return true;
	}

	public function getCronjobPath(){
		$oConfig = oxRegistry::get("oxConfig");
		return $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vexport";
	}

	static function csvData($file, $fieldsHead, $fields){
		if(!file_exists($file) && $fieldsHead != NULL){
			if( $fp = fopen($file ,'a+') ) {
				$csv = fputcsv($fp, $fieldsHead, chr(59), chr(0));
			}
			fclose($fp);
		}

		if( $fp = fopen($file ,'a+') ) {
			foreach($fields as $array) {
				$csv = fputcsv($fp, $array, chr(59), chr(34));
			}
			fclose($fp);
		} else {
			echo "Datei konnte nicht göffnet werden Pfad: " . $file;
		}
	
		return true;
	}

		/**
     *
     * @return void
     */

	static function setOrderEdit($oxidID) {
		$order = "ORDERFOLDER_FINISHED";
		$sQ = "UPDATE oxorder set OXFOLDER = 'ORDERFOLDER_FINISHED' where OXID = '". $oxidID ."' LIMIT 1";
		return $execute =  \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ);
	}

	static function logOrder ( $error, $id ) {
		$_sThisLogConfig = oxRegistry::get("oxConfig");
		$_sThisLogPath =  getShopBasePath() . "modules/ho_admin/log/";
		$_sThisLogPathData = $_sThisLogPath . "order.log";

		if(!$handle = fopen($_sThisLogPathData,"a")) {
			$this->setLog("system","Exportdatei konnte nicht geöffnet werden! Datei: 	$_sThisLogPathData");
		}

		switch($error) {
			case(0):
			$success = "Fehler: " . mysql_error() ."\n";
			break;
		
			case(1):
			$success = date("d.m.y H:i:s") . " - Bestellung " . $id . " () wurde exportiert\r\n"; 
			break;
			
			case(2):
			$success = date("d.m.y H:i:s") . " - Bestellung " . $id . " () wurde als erledigt gekennzeichnet\r\n"; 
			break;
		}
		fputs($handle, $success);
		fclose($handle);
	}

	public function setLog ( $logtype, $log ) {
		$_sThisLogConfig = oxRegistry::get("oxConfig");
		$_sThisLogPath =  getShopBasePath() . "modules/ho_admin/log/";
		$_sThisLogPathData = $_sThisLogPath . $logtype . ".log";

		if(!$handle = fopen($_sThisLogPathData,"a")) {
			echo "Konnte nicht geöffnet werden! Datei: 	$_sThisLogPathData";
		} else {
			$success = date("d.m.y H:i:s") . " - " . $log . "\r\n"; 
		}
		fputs($handle, $success);
		fclose($handle);
	}
}
?>