<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */

set_time_limit ( 180 );
 
class ho_import extends oxAdminView {
	/**
	 * Current class template name.
	 * @var string
	 */
	protected $_sThisTemplate = 'ho_import.tpl';
	/**
	 * 
	 * @var string
	 */
	public function render() {
		parent::render();

		return $this->_sThisTemplate;
	}

	public function setImportArticleCSV(){
		$_sThisImportConfig = oxRegistry::get("oxConfig");
		$_sThisImportCSV =  getShopBasePath() . $_sThisImportConfig->getConfigParam("HO_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HO_CSV_CSR_ARTICLE");
		
		$_sThisfSeek = ( isset($_GET['seek'])) ? $_GET['seek'] : 0;
		$_sThisSave = ( isset($_GET['save'])) ? $_GET['save'] : 0;
		$_sThisDel = ( isset($_GET['del'])) ? $_GET['del'] : 0;
		
		if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

			$array = array();
			fseek($jImportObject, $_sThisfSeek);

			while($jImportData = fgetcsv($jImportObject, 15000, chr(59),  chr(0) ) ){
				$array[] = $jImportData;

				if($i > 0){
					/* $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

					if(!$category->load(md5('Meine Artikel'))) {
						$oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
						$oDb->execute("INSERT INTO `oxcategories` ( oxid, oxtitle ) VALUES('" . md5('Meine Artikel') . "', 'Meine Artikel');");
					}  */

					$product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

					$_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

					if(!$product->load(md5($_sThisArtID))) {		
						$product->setId(md5($_sThisArtID));
					} else {
						$_sThisDel++;
						// ho_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
					}

						$product->oxarticles__oxartnum = new \OxidEsales\Eshop\Core\Field ( $array[$i][0] );
						$product->oxarticles__oxtitle = new \OxidEsales\Eshop\Core\Field( $array[$i][2] );
						$product->oxarticles__oxean = new \OxidEsales\Eshop\Core\Field( $array[$i][1] );
						// $product->oxarticles__oxdistean = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
						// $product->oxarticles__oxmpn = new \OxidEsales\Eshop\Core\Field( $array[$i][5] );
						$product->oxarticles__oxshortdesc = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
						$product->oxarticles__oxprice = new \OxidEsales\Eshop\Core\Field( $array[$i][5] );
						$product->oxarticles__oxvendorid = new \OxidEsales\Eshop\Core\Field( "3048509471044912d6ab1dd732cc362b" );
						$product->oxarticles__oxtemplate = new \OxidEsales\Eshop\Core\Field( "" );

						$vID = intval( substr( str_replace(" ", "", $array[$i][13]), -1) );
						$product->oxarticles__oxweight = new \OxidEsales\Eshop\Core\Field( $vID );

						$_oxpic1 = ($array[$i][6] !== "") ? "csr_" . basename($array[$i][6]) : "";
						$product->oxarticles__oxpic1 = new \OxidEsales\Eshop\Core\Field( $_oxpic1 );
						$_oxpic2 = ($array[$i][7] !== "") ? "csr_" . basename($array[$i][7]) : "";
						$product->oxarticles__oxpic2 = new \OxidEsales\Eshop\Core\Field( $_oxpic2 );
						$_oxpic3 = ($array[$i][8] !== "") ? "csr_" . basename($array[$i][8]) : "";
						$product->oxarticles__oxpic3 = new \OxidEsales\Eshop\Core\Field( $_oxpic3 );
						$_oxpic4 = ($array[$i][9] !== "") ? "csr_" . basename($array[$i][9]) : "";
						$product->oxarticles__oxpic4 = new \OxidEsales\Eshop\Core\Field( $_oxpic4 );
						$_oxpic5 = ($array[$i][10] !== "") ? "csr_" . basename($array[$i][10]) : "";
						$product->oxarticles__oxpic5 = new \OxidEsales\Eshop\Core\Field( $_oxpic5 );
						$_oxpic6 = ($array[$i][11] !== "") ? "csr_" . basename($array[$i][11]) : "";
						$product->oxarticles__oxpic6 = new \OxidEsales\Eshop\Core\Field( $_oxpic6 );
						$product->save();
						
						/* Set Longdescription */
						$oArtExt = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
						$oArtExt->init('oxartextends');
						$oArtExt->setId(md5( $_sThisArtID ));
						$oArtExt->oxartextends__oxlongdesc = new \OxidEsales\Eshop\Core\Field( $array[$i][4] );
						$oArtExt->save();

						ho_import::setLog("article", "Oxweight: " + $vID );

						/* Set Category */
						/* $oObject2Category = oxNew(\OxidEsales\Eshop\Application\Model\Object2Category::class);
						$oObject2Category->init('oxobject2category');

						$query = "SELECT `oxid` FROM `oxobject2category` WHERE `oxobjectid` = '" . md5($array[$i][1]) ."'";
						$resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query); */
						
						// Get the Result
						/* if ($resultSet != false && $resultSet->count() > 0) {
							while (!$resultSet->EOF) {
									$row = $resultSet->getFields();
									//do something
									$oObject2Category->load($row[0]);
									$resultSet->fetchRow();
								}
						} 

						$oObject2Category->setProductId(md5($array[$i][1]));
						$oObject2Category->setCategoryId(md5('Meine Artikel'));
						$oObject2Category->save(); */

						$oAttr = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
								$oAttr->setEnableMultilang(false);
						$oAttr->init('oxattribute');
						
						if (!$oAttr->load(md5("Versandkategorie"))) {
							$oAttr->setId(md5("Versandkategorie"));
							$oAttr->oxattribute__oxtitle = new \OxidEsales\Eshop\Core\Field("Versandkategorie");
							$oAttr->save();
						}

						$oObject2Attribute = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
						$oObject2Attribute->init("oxobject2attribute");

						if (!$oObject2Attribute->load(md5($_sThisArtID))) {
							$oObject2Attribute->setId(md5($_sThisArtID));
						}

						$oObject2Attribute->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field(md5($_sThisArtID));
						$oObject2Attribute->oxobject2attribute__oxattrid = new \OxidEsales\Eshop\Core\Field(md5("Versandkategorie"));
						$oObject2Attribute->oxobject2attribute__oxvalue = new \OxidEsales\Eshop\Core\Field($array[$i][13]);
						$oObject2Attribute->save();

						$_sThisSave++;
						
						$oConfig = oxRegistry::get("oxConfig");
						$_sThisUrl = $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=article&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&del=" . $_sThisDel;
						
						// ho_import::setLog ( "article", "Artikel " . $array[$i][2] . " " . $array[$i][0] . " wurde angelegt vID:" . $vID . " (".gettype ( $vID ).")- '" .$array[$i][13]."'" );

						if(($_sThisfSeek + 2000000) <= ftell($jImportObject)) {
							// ho_import::setLog("article", "###  Leite um zu " . $_sThisUrl ."  ###");
							header("Location:" . $_sThisUrl);
							exit;
						}

/* 					} else {
						// Artikel löschen
						$product->delete( md5($array[$i][1]) );
						ho_import::setLog ( "article", "Artikel " . $array[$i][2] . " " . $array[$i][0] . " wurde entfernt" );
						$del++;
					} */
				}
				$i++;
			}
			fclose($jImportObject);
			echo $_sThisSave . " Artikel angelegt, " . $_sThisDel ." Artikel gel&ouml;scht." ;
		}
		else {
			echo "Konnte Datei nicht korrekt auslesen";
		}
	}

	public function setImportRiegerArticleCSV(){
		$_sThisImportConfig = oxRegistry::get("oxConfig");
		$_sThisImportCSV =  getShopBasePath() . $_sThisImportConfig->getConfigParam("HO_IMPORT_RIEGER_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HO_CSV_RIEGER_ARTICLE");
		
		$_sThisfSeek = ( isset($_GET['seek'])) ? $_GET['seek'] : 0;
		$_sThisSave = ( isset($_GET['save'])) ? $_GET['save'] : 0;
		$_sThisEdit = ( isset($_GET['edit'])) ? $_GET['edit'] : 0;
		$_sThisDel = ( isset($_GET['del'])) ? $_GET['del'] : 0;
		
		if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

			$array = array();
			fseek($jImportObject, $_sThisfSeek);

			while($jImportData = fgetcsv($jImportObject, 15000, ';', '"' ) ){
				$array[] = $jImportData;

				if($i > 0){

					$product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

					$_sThisArtID = $array[$i][0];
					$_sThisPrice = $array[$i][1];
					$_sThisComission = $array[$i][2];
					$_sThisTitle = $array[$i][3];
					$_sThisShortDesc = $array[$i][5];
					$_sThisLongDesc = $array[$i][16];
					$vID = $array[$i][24];

					if(!$product->load(md5($_sThisArtID))) {		
						$product->setId(md5($_sThisArtID));
					} else {
						if($_sThisComission < 10) {
							$product->delete( md5($_sThisArtID) );
							ho_import::setLog("riegerarticle", "Artikel $_sThisArtID - $_sThisTitle gelöscht wegen zu niedriger Provision");
							$_sThisDel++;
						}
						$_sThisEdit++;
						// ho_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
					}

					if($_sThisComission >= 10) {

						$product->oxarticles__oxartnum = new \OxidEsales\Eshop\Core\Field ( $_sThisArtID );
						$product->oxarticles__oxtitle = new \OxidEsales\Eshop\Core\Field( $_sThisTitle );
						$product->oxarticles__oxean = new \OxidEsales\Eshop\Core\Field( "" );
						// $product->oxarticles__oxdistean = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
						$product->oxarticles__oxmpn = new \OxidEsales\Eshop\Core\Field( $array[$i][2] );
						$product->oxarticles__oxshortdesc = new \OxidEsales\Eshop\Core\Field( $_sThisShortDesc );
						$product->oxarticles__oxprice = new \OxidEsales\Eshop\Core\Field( $_sThisPrice );
						$product->oxarticles__oxvendorid = new \OxidEsales\Eshop\Core\Field( "19dcae3e2d69b2bc1debacea577f686a" );
						$product->oxarticles__oxtemplate = new \OxidEsales\Eshop\Core\Field( "" );

						$product->oxarticles__oxweight = new \OxidEsales\Eshop\Core\Field( $vID );

						$_oxpic1 = ($array[$i][26] !== "") ? "rieger_" . basename($array[$i][26]) : "";
						$product->oxarticles__oxpic1 = new \OxidEsales\Eshop\Core\Field( $_oxpic1 );
						$_oxpic2 = ($array[$i][27] !== "") ? "rieger_" . basename($array[$i][27]) : "";
						$product->oxarticles__oxpic2 = new \OxidEsales\Eshop\Core\Field( $_oxpic2 );
						$_oxpic3 = ($array[$i][28] !== "") ? "rieger_" . basename($array[$i][28]) : "";
						$product->oxarticles__oxpic3 = new \OxidEsales\Eshop\Core\Field( $_oxpic3 );
						$_oxpic4 = ($array[$i][29] !== "") ? "rieger_" . basename($array[$i][29]) : "";
						$product->oxarticles__oxpic4 = new \OxidEsales\Eshop\Core\Field( $_oxpic4 );
						$_oxpic5 = ($array[$i][30] !== "") ? "rieger_" . basename($array[$i][30]) : "";
						$product->oxarticles__oxpic5 = new \OxidEsales\Eshop\Core\Field( $_oxpic5 );
						$_oxpic6 = ($array[$i][31] !== "") ? "rieger_" . basename($array[$i][31]) : "";
						$product->oxarticles__oxpic6 = new \OxidEsales\Eshop\Core\Field( $_oxpic6 );
						$_oxpic7 = ($array[$i][32] !== "") ? "rieger_" . basename($array[$i][32]) : "";
						$product->oxarticles__oxpic7 = new \OxidEsales\Eshop\Core\Field( $_oxpic7 );
						$_oxpic8 = ($array[$i][33] !== "") ? "rieger_" . basename($array[$i][33]) : "";
						$product->oxarticles__oxpic8 = new \OxidEsales\Eshop\Core\Field( $_oxpic8 );
						$_oxpic9 = ($array[$i][34] !== "") ? "rieger_" . basename($array[$i][34]) : "";
						$product->oxarticles__oxpic9 = new \OxidEsales\Eshop\Core\Field( $_oxpic9 );
						$product->save();
						
						/* Set Longdescription */
						$oArtExt = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
						$oArtExt->init('oxartextends');
						$oArtExt->setId(md5( $_sThisArtID ));
						$oArtExt->oxartextends__oxlongdesc = new \OxidEsales\Eshop\Core\Field( $_sThisLongDesc );
						$oArtExt->save();

						ho_import::setLog("article", "Oxweight: " + $vID );

						$oAttr = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
								$oAttr->setEnableMultilang(false);
						$oAttr->init('oxattribute');
						
						if (!$oAttr->load(md5("Versandkategorie"))) {
							$oAttr->setId(md5("Versandkategorie"));
							$oAttr->oxattribute__oxtitle = new \OxidEsales\Eshop\Core\Field("Versandkategorie");
							$oAttr->save();
						}

						$oObject2Attribute = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
						$oObject2Attribute->init("oxobject2attribute");

						if (!$oObject2Attribute->load(md5($_sThisArtID))) {
							$oObject2Attribute->setId(md5($_sThisArtID));
						}

						$oObject2Attribute->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field(md5($_sThisArtID));
						$oObject2Attribute->oxobject2attribute__oxattrid = new \OxidEsales\Eshop\Core\Field(md5("Versandkategorie"));
						$oObject2Attribute->oxobject2attribute__oxvalue = new \OxidEsales\Eshop\Core\Field($vID);
						$oObject2Attribute->save();

						$_sThisSave++;
						
						$oConfig = oxRegistry::get("oxConfig");
						$_sThisUrl = $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=rieger-article&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&edit=" . $_sThisEdit . "&del=" . $_sThisDel;
						
						ho_import::setLog ( "riegerarticle", "Artikel " . $array[$i][2] . " " . $array[$i][0] . " wurde angelegt vID:" . $vID . " (".gettype ( $vID ).")- '" .$array[$i][13]."'" );

						if(($_sThisfSeek + 2000000) <= ftell($jImportObject)) {
							// ho_import::setLog("article", "###  Leite um zu " . $_sThisUrl ."  ###");
							header("Location:" . $_sThisUrl);
							exit;
						}

					}
				}
				$i++;
			}
			fclose($jImportObject);
			echo "$_sThisSave Artikel angelegt, $_sThisEdit Artikel bearbeitet, $_sThisDel Artikel gel&ouml;scht." ;
		}
		else {
			echo "Konnte Datei nicht korrekt auslesen";
		}
	}

	public function setImportStockCSV(){
		$_sThisImportStockConfig = oxRegistry::get("oxConfig");
		$_sThisImportStockCSV =  getShopBasePath() . $_sThisImportStockConfig->getConfigParam("HO_IMPORT_CSR_STOCK_PATH") . $_sThisImportStockConfig->getConfigParam("HO_CSV_CSR_STOCK");

		if (($jImportObject = fopen($_sThisImportStockCSV, "r")) !== FALSE) {
			fseek($jImportObject, 0);

			$i = 0;
			$array = array();

			while($jImportData = fgetcsv($jImportObject, 10000, chr(59),  chr(0) ) ){
				$array[] = $jImportData;
				if($i > 0){

					$product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

					$_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

					if(!$product->load(md5($_sThisArtID))) {
						$_sThisNotFound++;		
						// $product->setId(md5($_sThisArtID));
					} else {
						$_sThisEdit++;
						// ho_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
						$product->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field ( $array[$i][3] );
						$product->save();				
					}
				}
				$i++;
			}
			fclose($jImportObject);
			ho_import::setLog("stock", "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden");
			echo "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden";
		} else {
			echo "Keine Datei zum öffnen gefunden";
		}
	}

	public function setImportImagesCSV(){
		$_sThisImportConfig = oxRegistry::get("oxConfig");
		$_sThisImportCSV =  getShopBasePath() . $_sThisImportConfig->getConfigParam("HO_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HO_CSV_CSR_ARTICLE");
		
		$picturePath = getShopBasePath() . "/out/pictures/master/product/";
		$_sThisfSeek = ( isset($_GET['seek'])) ? $_GET['seek'] : 0;
		$_sThisPicSuccess = ( isset($_GET['save'])) ? $_GET['save'] : 0;
		$_sThisPicExists = ( isset($_GET['exs'])) ? $_GET['exs'] : 0;
		
		if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

			$array = array();
			fseek($jImportObject, $_sThisfSeek);

			while($jImportData = fgetcsv($jImportObject, 10000, chr(59),  chr(0) ) ){
				$array[] = $jImportData;

				if($i > 0){
					$_sThisSave++;
					// ho_import::setLog("picture", "Artikel " . $array[$i][2] . " Hat neue Bilder .. Seek :" . ($_sThisfSeek + 2000000) . " < " . ftell($jImportObject));
					$_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

					for($c = 1; $c < 7; $c++){
						$d = $c + 5;
						$_sThisTarPic = $array[$i][$d];
						$_sThisDestPic = $picturePath . $c . "/csr_" .basename($array[$i][$d]);
						if(!file_exists($_sThisDestPic)) {
							if(!copy($_sThisTarPic, $_sThisDestPic )){
								ho_import::setLog("riegerpicture", $i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
							} else {
								// ho_import::setLog("picture", $i . ". Bild: " . $_sThisTarPic . " wurde nach " . $_sThisDestPic . " kopiert");
								$_sThisPicSuccess++;
							}
						} else {
							// ho_import::setLog("picture", $i . ". Bild: " . $_sThisDestPic . " existiert bereits");
							$_sThisPicExists++;
						}
					}

					if(($_sThisfSeek + 1000000) <= ftell($jImportObject)) {
						$oConfig = oxRegistry::get("oxConfig");
						// ho_import::setLog("picture", "#########  Jetzt würde ich umleiten zu " . $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&del=" . $_sThisDel ."  ##########");
						header("Location:" . $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisPicSuccess . "&exs=" . $_sThisPicExists);
						// ho_import::setLog("picture", "Exit");
						exit;
					}
				}
				$i++;
			}
			fclose($jImportObject);
			echo $_sThisPicSuccess . " Bilder wurden heruntergeladen, " . $_sThisPicExists . " Bilder existierten bereits" ;
		}
		else {
			echo "Konnte Datei nicht korrekt auslesen";
		}
	}

	public function setImportRiegerImagesCSV(){
		$_sThisImportConfig = oxRegistry::get("oxConfig");
		$_sThisImportCSV =  getShopBasePath() . $_sThisImportConfig->getConfigParam("HO_IMPORT_RIEGER_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HO_CSV_RIEGER_ARTICLE");
		
		$picturePath = getShopBasePath() . "/out/pictures/master/product/";
		$_sThisfSeek = ( isset($_GET['seek'])) ? $_GET['seek'] : 0;
		$_sThisPicSuccess = ( isset($_GET['save'])) ? $_GET['save'] : 0;
		$_sThisPicExists = ( isset($_GET['exs'])) ? $_GET['exs'] : 0;
		
		if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

			$array = array();
			fseek($jImportObject, $_sThisfSeek);

			while($jImportData = fgetcsv($jImportObject, 15000, ';',  '"' ) ){
				$array[] = $jImportData;

				if($i > 0){
					$_sThisSave++;
					// ho_import::setLog("picture", "Artikel " . $array[$i][2] . " Hat neue Bilder .. Seek :" . ($_sThisfSeek + 2000000) . " < " . ftell($jImportObject));
					$_sThisArtID = $array[$i][0];

					for($c = 1; $c < 9; $c++){
						$d = $c + 25;
						$_sThisTarPic = $array[$i][$d];
						$_sThisDestPic = $picturePath . $c . "/rieger_" .basename($array[$i][$d]);
						if(!file_exists($_sThisDestPic)) {
							if(!copy($_sThisTarPic, $_sThisDestPic )){
								ho_import::setLog("picture", $i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
							} else {
								// ho_import::setLog("picture", $i . ". Bild: " . $_sThisTarPic . " wurde nach " . $_sThisDestPic . " kopiert");
								$_sThisPicSuccess++;
							}
						} else {
							// ho_import::setLog("picture", $i . ". Bild: " . $_sThisDestPic . " existiert bereits");
							$_sThisPicExists++;
						}
					}

					if(($_sThisfSeek + 1000000) <= ftell($jImportObject)) {
						$oConfig = oxRegistry::get("oxConfig");
						// ho_import::setLog("picture", "#########  Jetzt würde ich umleiten zu " . $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&del=" . $_sThisDel ."  ##########");
						header("Location:" . $oConfig->getShopUrl(null,false) . "index.php?cl=ho_vimport&action=rieger-picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisPicSuccess . "&exs=" . $_sThisPicExists);
						// ho_import::setLog("picture", "Exit");
						exit;
					}
				}
				$i++;
			}
			fclose($jImportObject);
			echo $_sThisPicSuccess . " Bilder wurden heruntergeladen, " . $_sThisPicExists . " Bilder existierten bereits" ;
		}
		else {
			echo "Konnte Datei nicht korrekt auslesen";
		}
	}

	public function setShippingID(){
		$query = "SELECT a.`oxid`, o.`oxvalue` FROM `oxarticles` AS a JOIN `oxobject2attribute` AS o ON a.`oxid` = o.`oxobjectid`";
		$resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
					
		// Get the Result
		if ($resultSet != false && $resultSet->count() > 0) {
			while (!$resultSet->EOF) {
				$row = $resultSet->getFields();
				$vID = intval( substr( str_replace(" ", "", $row[1]), -1) );
				$sQ = "UPDATE `oxarticles` SET `oxweight` = " . $vID ." WHERE `oxid` = '" . $row[0] . "';";
				$result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ);
				// ho_import::setLog("article", "Bearbeitete Artikel:" . $sQ);
				$resultSet->fetchRow();
			}
		} 
	}

	public function getCsvCount($key) {
		$_sThisImportConfig = oxRegistry::get("oxConfig");
		switch($key) {
			case("article"): $_sThisimportFile = $_sThisImportConfig->getConfigParam("HO_CSV_CSR_ARTICLE"); break;
			case("picture"): $_sThisimportFile = $_sThisImportConfig->getConfigParam("HO_CSV_CSR_PICTURE"); break;
		}
		$_sThisImportCSV =  getShopBasePath() . $_sThisImportConfig->getConfigParam("HO_IMPORT_CSR_ARTICLE_PATH") . $_sThisimportFile;
		
		return count(fopen($_sThisImportCSV, "r"));
	}

	public function getCsvData($csv) {
		$_sThisExportConfig = oxRegistry::get("oxConfig");
		$_sThisExportPath =  getShopBasePath() . $_sThisExportConfig->getConfigParam("HO_IMPORT_PATH");

		switch($csv) {
			case("article"): 	$_sThisExportFile = 	$_sThisExportConfig->getConfigParam("HO_CSV_CSR_ARTICLE"); break;
			case("user"): $_sThisExportFile = 	$_sThisExportConfig->getConfigParam("HO_CSV_CSR_USER"); break;
			case("picture"): $_sThisExportFile = 	$_sThisExportConfig->getConfigParam("HO_CSV_CSR_ARTICLE"); break;
		}

		if($log = file_get_contents($_sThisExportPath . $_sThisExportFile, true)){
			return $log;
		}else{
			return "Datei wurde nicht gefunden " . $_sThisExportPath . $_sThisExportFile . ".csv";
		}
	}

	public function setLog ( $logtype, $log ) {
		$_sThisLogConfig = oxRegistry::get("oxConfig");
		$_sThisLogPath =  getShopBasePath() . "log/ho_admin/";
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