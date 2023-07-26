<?php
/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */

namespace HDNET\hdadmin\admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\Eshop\Core\Registry;

set_time_limit(180);

class hd_import extends AdminController
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'hd_import.tpl';

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisPicturePath = '/out/pictures/master/product/';

    /**
     *
     * @var string
     */
    public function render()
    {
        parent::render();

        if ($_FILES['datei']) {
            $this->setImportFile($_FILES, $_POST);
        }
        return $this->_sThisTemplate;
    }

    public function setImportArticleCSV()
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");
        $articles = hd_import::getAllCSRArticleIDs();
        $new = 0;
        $edit = 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            while ($jImportData = fgetcsv($jImportObject, 15000, chr(59), chr(0))) {
                if ($i > 0) {
                    try {
                        $article = hd_import::setImportArticle($jImportData);
                        if ($article['count'] === 1) {
                            $new++;
                        } else {
                            $edit++;
                        }
                    } catch (\Exception $e) {
                        hd_import::setLog("article", "$e");
                    }
                }
                $checkArticle = \md5($article['article']);
                unset($articles[$checkArticle]);
                $i++;
            }
            $oldArticles = hd_import::deleteOldArticles($articles);
            echo "Es wurden $new Artikel angelegt und $edit bearbeitet. $oldArticles alte Artikel wurden gelöscht.";
        }
    }

    public function setImportArticle($array)
    {
        $article = [
            'oxid' => md5($array[0] . $array[1] . $array[2]),
            'oxartnum' => $array[0],
            'oxean' => $array[1],
            'oxtitle' => $array[2],
            'oxshortdesc' => $array[3],
            'oxlongdesc' => $array[4],
            'oxprice' => $array[5],
            'oxpic1' => ($array[6] !== "") ? "csr_" . basename($array[6]) : "",
            'oxpic2' => ($array[7] !== "") ? "csr_" . basename($array[7]) : "",
            'oxpic3' => ($array[8] !== "") ? "csr_" . basename($array[8]) : "",
            'oxpic4' => ($array[9] !== "") ? "csr_" . basename($array[9]) : "",
            'oxpic5' => ($array[10] !== "") ? "csr_" . basename($array[10]) : "",
            'oxpic6' => ($array[11] !== "") ? "csr_" . basename($array[11]) : "",
            'oxshippingcat' => $array[13]
        ];

        $status['count'] = 0;

        $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        if (!$product->load($article['oxid'])) {
            $product->setId($article['oxid']);
            $status['count']++;
        }

        $product->oxarticles__oxartnum = new Field ($article['oxartnum']);
        $product->oxarticles__oxtitle = new Field($article['oxtitle']);
        $product->oxarticles__oxean = new Field($article['oxean']);
        $product->oxarticles__oxshortdesc = new Field($article['oxshortdesc']);
        $product->oxarticles__oxprice = new Field($article['oxprice']);
        $product->oxarticles__oxvendorid = new Field("3048509471044912d6ab1dd732cc362b");
        $product->oxarticles__oxweight = new Field(hd_import::getShippingValue($article['oxshippingcat']));
        $product->oxarticles__oxpic1 = new Field($article['oxpic1']);
        $product->oxarticles__oxpic2 = new Field($article['oxpic2']);
        $product->oxarticles__oxpic3 = new Field($article['oxpic3']);
        $product->oxarticles__oxpic4 = new Field($article['oxpic4']);
        $product->oxarticles__oxpic5 = new Field($article['oxpic5']);
        $product->oxarticles__oxpic6 = new Field($article['oxpic6']);
        $product->save();

        # Set Longdescription
        hd_import::setLongDesc($article['oxid'], $article['oxlongdesc']);

        # Versandkategorie
        hd_import::checkAttributeAndSet("Versandkategorie");
        hd_import::setObject2Attribute($article['oxid'], "Versandkategorie", $article['oxshippingcat']);

        $status['article'] = $article['oxid'];
        return $status;
    }

    public function setImportArticleOld()
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");

        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisSave = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisDel = (isset($_GET['del'])) ? $_GET['del'] : 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

            $array = array();
            fseek($jImportObject, $_sThisfSeek);

            while ($jImportData = fgetcsv($jImportObject, 15000, chr(59), chr(0))) {
                $array[] = $jImportData;

                if ($i > 0) {
                    $_sThisArtNum = $array[$i][0];
                    $_sThisTitle = $array[$i][2];
                    $_sThisShipping = $array[$i][13];
                    $_sThisLongDesc = $array[$i][4];
                    $_sThisShippCat = $array[$i][13];

                    $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                    $_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

                    if (!$product->load(md5($_sThisArtID))) {
                        $product->setId(md5($_sThisArtID));
                    } else {
                        $_sThisEdit++;
                    }

                    $product->oxarticles__oxartnum = new Field ($_sThisArtNum);
                    $product->oxarticles__oxtitle = new Field($array[$i][2]);
                    $product->oxarticles__oxean = new Field($array[$i][1]);
                    // $product->oxarticles__oxdistean = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
                    // $product->oxarticles__oxmpn = new \OxidEsales\Eshop\Core\Field( $array[$i][5] );
                    $product->oxarticles__oxshortdesc = new Field($array[$i][3]);
                    $product->oxarticles__oxprice = new Field($array[$i][5]);
                    $product->oxarticles__oxvendorid = new Field("3048509471044912d6ab1dd732cc362b");
                    $product->oxarticles__oxtemplate = new Field("");
                    $product->oxarticles__oxweight = new Field(hd_import::getShippingValue($_sThisShipping));

                    $_oxpic1 = ($array[$i][6] !== "") ? "csr_" . basename($array[$i][6]) : "";
                    $product->oxarticles__oxpic1 = new Field($_oxpic1);
                    $_oxpic2 = ($array[$i][7] !== "") ? "csr_" . basename($array[$i][7]) : "";
                    $product->oxarticles__oxpic2 = new Field($_oxpic2);
                    $_oxpic3 = ($array[$i][8] !== "") ? "csr_" . basename($array[$i][8]) : "";
                    $product->oxarticles__oxpic3 = new Field($_oxpic3);
                    $_oxpic4 = ($array[$i][9] !== "") ? "csr_" . basename($array[$i][9]) : "";
                    $product->oxarticles__oxpic4 = new Field($_oxpic4);
                    $_oxpic5 = ($array[$i][10] !== "") ? "csr_" . basename($array[$i][10]) : "";
                    $product->oxarticles__oxpic5 = new Field($_oxpic5);
                    $_oxpic6 = ($array[$i][11] !== "") ? "csr_" . basename($array[$i][11]) : "";
                    $product->oxarticles__oxpic6 = new Field($_oxpic6);
                    $product->save();

                    # Set Longdescription
                    hd_import::setLongDesc($_sThisArtID, $_sThisLongDesc);

                    # Versandkategorie
                    hd_import::checkAttributeAndSet("Versandkategorie");
                    hd_import::setObject2Attribute($_sThisArtID, "Versandkategorie", $_sThisShippCat);

                    $_sThisSave++;

                    $oConfig = Registry::get("oxConfig");
                    $_sThisUrl = $oConfig->getShopUrl(null, false) . "index.php?cl=hd_vimport&action=article&seek=" . ftell($jImportObject) . "&save=" . $_sThisSave . "&edit=" . $_sThisEdit;

                    hd_import::setLog("csrarticle", "Artikel (" . $_sThisArtNum . ") " . $_sThisTitle . " wurde angelegt");

                    if (($_sThisfSeek + 2000000) <= ftell($jImportObject)) {
                        // hd_import::setLog("article", "###  Leite um zu " . $_sThisUrl ."  ###");
                        header("Location:" . $_sThisUrl);
                        exit;
                    }
                }
                $i++;
            }
            fclose($jImportObject);
            echo $_sThisSave . " Artikel angelegt, " . $_sThisEdit . " Artikel bearbeitet.";
        } else {
            echo "Konnte CSR (" . $_sThisImportCSV . ") nicht korrekt auslesen";
        }
    }

    public function setImportRiegerArticleCSV()
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_RIEGER_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_RIEGER_ARTICLE");

        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisSave = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisEdit = (isset($_GET['edit'])) ? $_GET['edit'] : 0;
        $_sThisDel = (isset($_GET['del'])) ? $_GET['del'] : 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

            $array = array();

            fseek($jImportObject, $_sThisfSeek);

            while ($jImportData = fgetcsv($jImportObject, 15000, ';', '"')) {
                $array[] = $jImportData;

                if ($i > 0) {

                    $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                    $_sThisArtID = $array[$i][0];
                    $_sThisPrice = $array[$i][1];
                    $_sThisComission = $array[$i][2];
                    $_sThisTitle = $array[$i][3];
                    $_sThisShortDesc = $array[$i][5];
                    $_sThisLongDesc = $array[$i][16];
                    $_sThisCarBrand = $array[$i][7];
                    $_sThisCarType = $array[$i][8];
                    $_sThisStock = ($array[$i][40] == "yes") ? 9999 : 0;
                    $_sThisShipping = $array[$i][24];

                    if (!$product->load(md5($_sThisArtID))) {
                        $product->setId(md5($_sThisArtID));
                    } else {
                        if ($_sThisComission < 10) {
                            $product->delete(md5($_sThisArtID));
                            hd_import::setLog("riegerarticle", "Artikel $_sThisArtID - $_sThisTitle gelöscht wegen zu niedriger Provision");
                            $_sThisDel++;
                        }
                        $_sThisEdit++;
                        // hd_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
                    }

                    if ($_sThisComission >= 10) {

                        $product->oxarticles__oxartnum = new Field ($_sThisArtID);
                        $product->oxarticles__oxtitle = new Field($_sThisTitle);
                        $product->oxarticles__oxean = new Field("");
                        // $product->oxarticles__oxdistean = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
                        $product->oxarticles__oxmpn = new Field($array[$i][2]);
                        $product->oxarticles__oxshortdesc = new Field($_sThisShortDesc);
                        $product->oxarticles__oxprice = new Field($_sThisPrice);
                        $product->oxarticles__oxvendorid = new Field("19dcae3e2d69b2bc1debacea577f686a");
                        $product->oxarticles__oxstock = new Field($_sThisStock);
                        $product->oxarticles__oxtemplate = new Field("");
                        $product->oxarticles__oxweight = new Field(hd_import::getShippingValue($_sThisShipping));

                        $_oxpic1 = ($array[$i][26] !== "") ? "rieger_" . basename($array[$i][26]) : "";
                        $product->oxarticles__oxpic1 = new Field($_oxpic1);
                        $_oxpic2 = ($array[$i][27] !== "") ? "rieger_" . basename($array[$i][27]) : "";
                        $product->oxarticles__oxpic2 = new Field($_oxpic2);
                        $_oxpic3 = ($array[$i][28] !== "") ? "rieger_" . basename($array[$i][28]) : "";
                        $product->oxarticles__oxpic3 = new Field($_oxpic3);
                        $_oxpic4 = ($array[$i][29] !== "") ? "rieger_" . basename($array[$i][29]) : "";
                        $product->oxarticles__oxpic4 = new Field($_oxpic4);
                        $_oxpic5 = ($array[$i][30] !== "") ? "rieger_" . basename($array[$i][30]) : "";
                        $product->oxarticles__oxpic5 = new Field($_oxpic5);
                        $_oxpic6 = ($array[$i][31] !== "") ? "rieger_" . basename($array[$i][31]) : "";
                        $product->oxarticles__oxpic6 = new Field($_oxpic6);
                        $_oxpic7 = ($array[$i][32] !== "") ? "rieger_" . basename($array[$i][32]) : "";
                        $product->oxarticles__oxpic7 = new Field($_oxpic7);
                        $_oxpic8 = ($array[$i][33] !== "") ? "rieger_" . basename($array[$i][33]) : "";
                        $product->oxarticles__oxpic8 = new Field($_oxpic8);
                        $_oxpic9 = ($array[$i][34] !== "") ? "rieger_" . basename($array[$i][34]) : "";
                        $product->oxarticles__oxpic9 = new Field($_oxpic9);
                        $product->save();

                        # Set Longdescription
                        hd_import::setLongDesc($_sThisArtID, $_sThisLongDesc);

                        # Versandkategorie
                        hd_import::checkAttributeAndSet("Versandkategorie");
                        hd_import::setObject2Attribute($_sThisArtID, "Versandkategorie", $_sThisShipping);

                        # Marke
                        hd_import::checkAttributeAndSet("Marke");
                        hd_import::setObject2Attribute($_sThisArtID, "Marke", $_sThisCarBrand);

                        # Modell
                        hd_import::checkAttributeAndSet("Modell");
                        hd_import::setObject2Attribute($_sThisArtID, "Modell", $_sThisCarType);

                        $_sThisSave++;

                        $oConfig = Registry::get("oxConfig");
                        $_sThisUrl = $oConfig->getShopUrl(null, false) . "index.php?cl=hd_vimport&action=rieger-article&seek=" . ftell($jImportObject) . "&save=" . $_sThisSave . "&edit=" . $_sThisEdit . "&del=" . $_sThisDel;

                        hd_import::setLog("riegerarticle", "Artikel " . $_sThisTitle . " " . $_sThisArtID . " wurde angelegt vID:" . hd_import::getShippingValue($_sThisShipping) . "");

                        if (($_sThisfSeek + 2000000) <= ftell($jImportObject)) {
                            // hd_import::setLog("article", "###  Leite um zu " . $_sThisUrl ."  ###");
                            header("Location:" . $_sThisUrl);
                            exit;
                        }

                    }
                }
                $i++;
            }
            fclose($jImportObject);
            echo "$_sThisSave Artikel angelegt, $_sThisEdit Artikel bearbeitet, $_sThisDel Artikel gel&ouml;scht.";
        } else {
            echo "Konnte Rieger Datei nicht korrekt auslesen";
        }
    }

    public function setImportFkArticleCSV()
    {

        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_FK_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_FK_ARTICLE");

        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisSave = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisEdit = (isset($_GET['edit'])) ? $_GET['edit'] : 0;
        $_sThisDel = (isset($_GET['del'])) ? $_GET['del'] : 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            $array = array();
            fseek($jImportObject, $_sThisfSeek);

            while ($jImportData = fgetcsv($jImportObject, 15000, ';', '"')) {
                $array[] = $jImportData;

                if ($i > 0) {

                    $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                    $_sThisArtID = $array[$i][0];
                    $_sThisPrice = ($array[$i][4] * 1.16);
                    $_sThisComission = ((100 / $array[$i][4]) * ($array[$i][4] - $array[$i][22]));
                    $_sThisTitle = $array[$i][6];
                    $_sThisEAN = $array[$i][5];
                    $_sThisShortDesc = $array[$i][8];
                    $_sThisLongDesc = $array[$i][9];
                    $_sThisCarBrand = $array[$i][20];
                    $_sThisCarType = $array[$i][21];
                    $_sThisStock = $array[$i][1];
                    $_sThisShipping = $array[$i][30];
                    $_sThisKeyword = $array[$i][10];

                    if (!$product->load(md5($_sThisArtID))) {
                        $product->setId(md5($_sThisArtID));
                    } else {
                        if ($_sThisComission < 10) {
                            $product->delete(md5($_sThisArtID));
                            hd_import::setLog("fkarticle", "Artikel $_sThisArtID - $_sThisTitle gelöscht wegen zu niedriger Provision");
                            $_sThisDel++;
                        }
                        $_sThisEdit++;
                        // hd_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
                    }

                    if ($_sThisComission >= 10) {

                        $product->oxarticles__oxartnum = new Field ($_sThisArtID);
                        $product->oxarticles__oxtitle = new Field($_sThisTitle);
                        $product->oxarticles__oxean = new Field($_sThisEAN);
                        // $product->oxarticles__oxdistean = new \OxidEsales\Eshop\Core\Field( $array[$i][3] );
                        $product->oxarticles__oxmpn = new Field($array[$i][2]);
                        $product->oxarticles__oxshortdesc = new Field($_sThisShortDesc);
                        $product->oxarticles__oxprice = new Field($_sThisPrice);
                        $product->oxarticles__oxvendorid = new Field("8c8ba29dafd95af91e280d1e80b81773");
                        $product->oxarticles__oxstock = new Field($_sThisStock);
                        $product->oxarticles__oxtemplate = new Field("");
                        $product->oxarticles__oxsearchkeys = new Field($_sThisKeyword);
                        $product->oxarticles__oxweight = new Field(hd_import::getShippingValue($_sThisShipping));

                        $_oxpic1 = ($array[$i][11] !== "") ? "fk_" . basename($array[$i][11]) : "";
                        $product->oxarticles__oxpic1 = new Field($_oxpic1);
                        $_oxpic2 = ($array[$i][12] !== "") ? "fk_" . basename($array[$i][12]) : "";
                        $product->oxarticles__oxpic2 = new Field($_oxpic2);
                        $_oxpic3 = ($array[$i][13] !== "") ? "fk_" . basename($array[$i][13]) : "";
                        $product->oxarticles__oxpic3 = new Field($_oxpic3);
                        $_oxpic4 = ($array[$i][14] !== "") ? "fk_" . basename($array[$i][14]) : "";
                        $product->oxarticles__oxpic4 = new Field($_oxpic4);
                        $_oxpic5 = ($array[$i][15] !== "") ? "fk_" . basename($array[$i][15]) : "";
                        $product->oxarticles__oxpic5 = new Field($_oxpic5);
                        $_oxpic6 = ($array[$i][16] !== "") ? "fk_" . basename($array[$i][16]) : "";
                        $product->oxarticles__oxpic6 = new Field($_oxpic6);
                        $_oxpic7 = ($array[$i][17] !== "") ? "fk_" . basename($array[$i][17]) : "";
                        $product->oxarticles__oxpic7 = new Field($_oxpic7);
                        $_oxpic8 = ($array[$i][18] !== "") ? "fk_" . basename($array[$i][18]) : "";
                        $product->oxarticles__oxpic8 = new Field($_oxpic8);
                        $product->save();

                        # Set Longdescription
                        hd_import::setLongDesc($_sThisArtID, $_sThisLongDesc);

                        # Versandkategorie
                        hd_import::checkAttributeAndSet("Versandkategorie");
                        hd_import::setObject2Attribute($_sThisArtID, "Versandkategorie", $_sThisShipping);

                        # Marke
                        hd_import::checkAttributeAndSet("Marke");
                        hd_import::setObject2Attribute($_sThisArtID, "Marke", $_sThisCarBrand);

                        # Modell
                        hd_import::checkAttributeAndSet("Modell");
                        hd_import::setObject2Attribute($_sThisArtID, "Modell", $_sThisCarType);

                        $_sThisSave++;

                        $oConfig = Registry::get("oxConfig");
                        $_sThisUrl = $oConfig->getShopUrl(null, false) . "index.php?cl=hd_vimport&action=fk-article&seek=" . ftell($jImportObject) . "&save=" . $_sThisSave . "&edit=" . $_sThisEdit . "&del=" . $_sThisDel;

                        hd_import::setLog("fkarticle", "Artikel \"" . $_sThisTitle . "\" [" . $_sThisArtID . "] wurde angelegt vID:" . hd_import::getShippingValue($_sThisShipping) . "");

                        if (($_sThisfSeek + 2000000) <= ftell($jImportObject)) {
                            // hd_import::setLog("article", "###  Leite um zu " . $_sThisUrl ."  ###");
                            header("Location:" . $_sThisUrl);
                            exit;
                        }

                    }
                }
                $i++;
            }
            fclose($jImportObject);
            echo "$_sThisSave Artikel angelegt, $_sThisEdit Artikel bearbeitet, $_sThisDel Artikel gel&ouml;scht.";
        } else {
            echo "Konnte FK Datei nicht korrekt auslesen";
        }
    }

    public function setImportStockCSV()
    {
        $_sThisImportStockConfig = Registry::get("oxConfig");
        $_sThisImportStockCSV = getShopBasePath() . $_sThisImportStockConfig->getConfigParam("HD_IMPORT_CSR_STOCK_PATH") . $_sThisImportStockConfig->getConfigParam("HD_CSV_CSR_STOCK");

        if (($jImportObject = fopen($_sThisImportStockCSV, "r")) !== FALSE) {
            fseek($jImportObject, 0);

            $i = 0;
            $array = array();

            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[] = $jImportData;
                if ($i > 0) {

                    $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                    $_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

                    if (!$product->load(md5($_sThisArtID))) {
                        $_sThisNotFound++;
                        // $product->setId(md5($_sThisArtID));
                    } else {
                        $_sThisEdit++;
                        // hd_import::setLog("article", "Doppelter Artikel:" . $array[$i][0] . " - " . $array[$i][2]);
                        $product->oxarticles__oxstock = new Field ($array[$i][3]);
                        $product->save();
                    }
                }
                $i++;
            }
            fclose($jImportObject);
            hd_import::setLog("stock", "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden");
            echo "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden";
        } else {
            echo "Keine Datei zum öffnen gefunden";
        }
    }

    public function setImportImagesCSV()
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");

        $picturePath = getShopBasePath() . "/out/pictures/master/product/";
        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisPicSuccess = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisPicExists = (isset($_GET['exs'])) ? $_GET['exs'] : 0;

        $_sImportCount = 5;
        $_sImportStart = 6;

        // hd_import::setLog("picture", "Seek: " . $_sThisfSeek );
        try {
            $status = hd_import::importImages($_sThisImportCSV, $_sImportStart, $_sImportCount);
        } catch (Excetion $e) {
            hd_import::log('pictures', $e->getMessage());
        }

        echo "Es wurden " . $status['succes'] . " Bilder heruntergladen, " . $status['exist'] . " Bilder waren bereits vorhanden und " . $status['error'] . " Bilder konnten nicht herunter geladen werden.";

        /* if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

            $array = array();
            fseek($jImportObject, $_sThisfSeek);

            while($jImportData = fgetcsv($jImportObject, 10000, chr(59),  chr(0) ) ){
                $array[] = $jImportData;

                if($i > 0){
                    $_sThisSave++;
                    // hd_import::setLog("picture", "Artikel " . $array[$i][2] . " Hat neue Bilder .. Seek :" . ($_sThisfSeek + 2000000) . " < " . ftell($jImportObject));
                    $_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

                    for($c = 1; $c < 7; $c++){
                        $d = $c + 5;
                        $_sThisTarPic = $array[$i][$d];
                        $_sThisDestPic = $picturePath . $c . "/csr_" .basename($array[$i][$d]);
                        if(!file_exists($_sThisDestPic)) {
                            if(!copy($_sThisTarPic, $_sThisDestPic )){
                                hd_import::setLog("picture", $i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
                            } else {
                                hd_import::setLog("picture", $i . ". Bild: " . $_sThisTarPic . " wurde nach " . $_sThisDestPic . " kopiert");
                                $_sThisPicSuccess++;
                            }
                        } else {
                            hd_import::setLog("picture", $i . ". Bild: " . $_sThisDestPic . " existiert bereits");
                            $_sThisPicExists++;
                        }
                    }

                    if(($_sThisfSeek + 500000) <= ftell($jImportObject)) {
                        $oConfig = Registry::get("oxConfig");
                        $redirectURL = $oConfig->getShopUrl(null,false) . "index.php?cl=hd_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisPicSuccess . "&exs=" . $_sThisPicExists;

                        try {
                            hd_import::setLog("picture", "#########  Jetzt würde ich umleiten zu " . $redirectURL ."  ##########");
                            header("Location:" . $redirectURL);
                        } catch (Exception $e) {
                            hd_import::setLog("picture", "Umleitung nicht erfolgreich: " . $e->getMessage());
                        }
                        // hd_import::setLog("picture", "Exit");
                        exit;
                    }
                } else
                $i++;
            }
            fclose($jImportObject);
            echo $_sThisPicSuccess . " Bilder wurden heruntergeladen, " . $_sThisPicExists . " Bilder existierten bereits" ;
        }
        else {
            echo "Konnte Datei nicht korrekt auslesen";
        } */
    }

    public function setImportRiegerImagesCSV()
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_RIEGER_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_RIEGER_ARTICLE");


        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisPicSuccess = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisPicExists = (isset($_GET['exs'])) ? $_GET['exs'] : 0;
        $_sThisPicNotExists = (isset($_GET['notex'])) ? $_GET['notex'] : 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

            $array = array();
            fseek($jImportObject, $_sThisfSeek);

            while ($jImportData = fgetcsv($jImportObject, 15000, ';', '"')) {
                $array[] = $jImportData;

                if ($i > 0) {
                    // hd_import::setLog("picture", "Artikel " . $array[$i][2] . " Hat neue Bilder .. Seek :" . ($_sThisfSeek + 2000000) . " < " . ftell($jImportObject));
                    $_sThisArtID = $array[$i][0];

                    for ($c = 1; $c < 9; $c++) {
                        $d = $c + 25;
                        $_sThisTarPic = $array[$i][$d];
                        $_sThisDestPic = $picturePath . $c . "/rieger_" . basename($array[$i][$d]);

                        // echo hd_import::url_check($_sThisTarPic) . " <a target='_blank' href='$_sThisTarPic'>$_sThisTarPic</a><br>";

                        if (hd_import::url_check($_sThisTarPic) !== 0) {
                            if (!file_exists($_sThisDestPic)) {
                                if (!copy($_sThisTarPic, $_sThisDestPic)) {
                                    hd_import::setLog("riegerpicture", $i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
                                } else {
                                    hd_import::setLog("riegerpicture", $i . ". Bild: " . $_sThisTarPic . " wurde nach " . $_sThisDestPic . " kopiert");
                                    $_sThisPicSuccess++;
                                }
                            } else {
                                hd_import::setLog("riegerpicture", $i . ". Bild: " . $_sThisDestPic . " existiert bereits");
                                $_sThisPicExists++;
                            }
                        } else {
                            hd_import::setLog("riegerpicture", "$i - $_sThisTarPic Bild nicht vorhanden (404)");
                            $_sThisPicNotExists++;
                        }
                    }

                    if (($_sThisfSeek + 300000) <= ftell($jImportObject)) {
                        $oConfig = Registry::get("oxConfig");
                        // hd_import::setLog("picture", "#########  Jetzt würde ich umleiten zu " . $oConfig->getShopUrl(null,false) . "index.php?cl=hd_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&del=" . $_sThisDel ."  ##########");
                        header("Location:" . $oConfig->getShopUrl(null, false) . "index.php?cl=hd_vimport&action=rieger-picture&seek=" . ftell($jImportObject) . "&save=" . $_sThisPicSuccess . "&exs=" . $_sThisPicExists . "&notex=" . $_sThisPicNotExists);
                        // hd_import::setLog("picture", "Exit");
                        exit;
                    }
                }
                $i++;
            }
            fclose($jImportObject);
            echo $_sThisPicSuccess . " Bilder wurden heruntergeladen, " . $_sThisPicExists . " Bilder existierten bereits";
        } else {
            echo "Konnte Datei nicht korrekt auslesen";
        }
    }

    public function setImportFkImagesCSV()
    {

        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_FK_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_FK_ARTICLE");

        $picturePath = getShopBasePath() . "/out/pictures/master/product/";
        $_sThisfSeek = (isset($_GET['seek'])) ? $_GET['seek'] : 0;
        $_sThisPicSuccess = (isset($_GET['save'])) ? $_GET['save'] : 0;
        $_sThisPicExists = (isset($_GET['exs'])) ? $_GET['exs'] : 0;
        $_sThisPicNotExists = (isset($_GET['notex'])) ? $_GET['notex'] : 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {

            $array = array();
            fseek($jImportObject, $_sThisfSeek);

            while ($jImportData = fgetcsv($jImportObject, 15000, ';', '"')) {
                $array[] = $jImportData;

                if ($i > 0) {
                    // hd_import::setLog("picture", "Artikel " . $array[$i][2] . " Hat neue Bilder .. Seek :" . ($_sThisfSeek + 2000000) . " < " . ftell($jImportObject));
                    $_sThisArtID = $array[$i][0];

                    for ($c = 1; $c < 8; $c++) {
                        $d = $c + 10;
                        $_sThisTarPic = $array[$i][$d];
                        $_sThisDestPic = $picturePath . $c . "/fk_" . basename($array[$i][$d]);

                        // echo hd_import::url_check($_sThisTarPic) . " <a target='_blank' href='$_sThisTarPic'>$_sThisTarPic</a><br>";

                        if (hd_import::url_check($_sThisTarPic) !== 0) {
                            if (!file_exists($_sThisDestPic)) {
                                if (!copy($_sThisTarPic, $_sThisDestPic)) {
                                    hd_import::setLog("fkpicture", $i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
                                } else {
                                    hd_import::setLog("fkpicture", $i . ". Bild: " . $_sThisTarPic . " wurde nach " . $_sThisDestPic . " kopiert");
                                    $_sThisPicSuccess++;
                                }
                            } else {
                                hd_import::setLog("fkpicture", $i . ". Bild: " . $_sThisDestPic . " existiert bereits");
                                $_sThisPicExists++;
                            }
                        } else {
                            hd_import::setLog("fkpicture", "$i - $_sThisTarPic Bild nicht vorhanden (404)");
                            $_sThisPicNotExists++;
                        }
                    }

                    if (($_sThisfSeek + 300000) <= ftell($jImportObject)) {
                        $oConfig = Registry::get("oxConfig");
                        // hd_import::setLog("picture", "#########  Jetzt würde ich umleiten zu " . $oConfig->getShopUrl(null,false) . "index.php?cl=hd_vimport&action=picture&seek=" . ftell($jImportObject) ."&save=" . $_sThisSave . "&del=" . $_sThisDel ."  ##########");
                        header("Location:" . $oConfig->getShopUrl(null, false) . "index.php?cl=hd_vimport&action=fk-picture&seek=" . ftell($jImportObject) . "&save=" . $_sThisPicSuccess . "&exs=" . $_sThisPicExists . "&notex=" . $_sThisPicNotExists);
                        // hd_import::setLog("picture", "Exit");
                        exit;
                    }
                }
                $i++;
            }
            fclose($jImportObject);
            echo $_sThisPicSuccess . " Bilder wurden heruntergeladen, " . $_sThisPicExists . " Bilder existierten bereits";
        } else {
            echo "Konnte Datei nicht korrekt auslesen";
        }
    }

    public function deleteCSRArticles()
    {
        echo $this->_sThisPicturePath;

        $this->setLog('test', 'Testlog');
        // parent :: resetNrOfCatArticles();
    }

    public function setShippingID()
    {
        $query = "SELECT a.`oxid`, o.`oxvalue` FROM `oxarticles` AS a JOIN `oxobject2attribute` AS o ON a.`oxid` = o.`oxobjectid`";
        $resultSet = DatabaseProvider::getDb()->select($query);

        // Get the Result
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();
                $vID = intval(substr(str_replace(" ", "", $row[1]), -1));
                $sQ = "UPDATE `oxarticles` SET `oxweight` = " . $vID . " WHERE `oxid` = '" . $row[0] . "';";
                $result = DatabaseProvider::getDb()->execute($sQ);
                // hd_import::setLog("article", "Bearbeitete Artikel:" . $sQ);
                $resultSet->fetchRow();
            }
        }
    }

    public function getArticleNumbers($vendor)
    {
        $query = 'SELECT oxartnum from oxarticles where oxvendorid = "' . $vendor . '";';
        $resultSet = DatabaseProvider::getDb()->select($query);

        // Get the Result
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();
                $dbarticles[] = $row[0];
                $resultSet->fetchRow();
            }
        }
        return $dbarticles;
    }

    public function getCsvCount($key)
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        switch ($key) {
            case("article"):
                $_sThisimportFile = $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");
                break;
            case("picture"):
                $_sThisimportFile = $_sThisImportConfig->getConfigParam("HD_CSV_CSR_PICTURE");
                break;
        }
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisimportFile;

        return count(fopen($_sThisImportCSV, "r"));
    }

    public function getCsvData($csv)
    {
        $_sThisExportConfig = Registry::get("oxConfig");
        $_sThisExportPath = getShopBasePath() . $_sThisExportConfig->getConfigParam("HD_IMPORT_PATH");

        switch ($csv) {
            case("article"):
                $_sThisExportFile = $_sThisExportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");
                break;
            case("user"):
                $_sThisExportFile = $_sThisExportConfig->getConfigParam("HD_CSV_CSR_USER");
                break;
            case("picture"):
                $_sThisExportFile = $_sThisExportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");
                break;
        }

        if ($log = file_get_contents($_sThisExportPath . $_sThisExportFile, true)) {
            return $log;
        } else {
            return "Datei wurde nicht gefunden " . $_sThisExportPath . $_sThisExportFile . ".csv";
        }
    }

    public function setLog($logtype, $log)
    {
        $_sThisLogPath = getShopBasePath() . "log/hd_admin/";
        $_sThisLogPathData = $_sThisLogPath . $logtype . ".log";

        if (!$handle = fopen($_sThisLogPathData, "a")) {
            echo "Konnte nicht geöffnet werden! Datei: 	$_sThisLogPathData";
        } else {
            $success = date("d.m.y H:i:s") . " - " . $log . "\r\n";
        }
        fputs($handle, $success);
        fclose($handle);
    }

    public function url_check($url)
    {
        $hdrs = @get_headers($url);
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false;
    }

    public function getShippingValue($value)
    {
        switch ($value) {
            case("Shop Kat 5"):
                $shoppingValue = 1;
                break;
            case("Shop Kat 4"):
                $shoppingValue = 100;
                break;
            case("3 - Spedition"):
                $shoppingValue = 1000000;
                break;
            case("4 - DHL Sperrgut"):
                $shoppingValue = 1000000;
                break;
            case("IngoNoak Kat5"):
                $shoppingValue = 10000;
                break;
            case("1 - DHL Paket"):
                $shoppingValue = 100;
                break;
            case("1 - GLS"):
                $shoppingValue = 100;
                break;
            case("Kategorie 2"):
                $shoppingValue = 100;
                break;
            case("Shop Kat 3"):
                $shoppingValue = 100;
                break;
            case("Shop Kat 1"):
                $shoppingValue = 100000000;
                break;
            case("Shop Kat 0"):
                $shoppingValue = 10000;
                break;
            case("Shop Kat 2"):
                $shoppingValue = 1000000;
                break;
            case("IngoNoak Kat3"):
                $shoppingValue = 1;
                break;
            case("Versandkostenfrei in alle Länder"):
                $shoppingValue = 0;
                break;
            case("0 EUR"):
                $shoppingValue = 1;
                break;
            case("6.64 EUR"):
                $shoppingValue = 1;
                break;
            case("7.7 EUR"):
                $shoppingValue = 1;
                break;
            case("29.93 EUR"):
                $shoppingValue = 1000000;
                break;
            default:
                hd_import::setLog("shipping", "Konnte $value nicht finden");
        }
        return $shoppingValue;
    }

    public function setLongDesc($id, $desc)
    {
        $oArtExt = oxNew(MultiLanguageModel::class);
        $oArtExt->init('oxartextends');
        $oArtExt->setId(md5($id));
        $oArtExt->oxartextends__oxlongdesc = new Field($desc);
        $oArtExt->save();
    }

    public function checkAttributeAndSet($value)
    {
        $oAttr = oxNew(MultiLanguageModel::class);
        $oAttr->setEnableMultilang(false);
        $oAttr->init('oxattribute');

        if (!$oAttr->load(md5($value))) {
            $oAttr->setId(md5($value));
            $oAttr->oxattribute__oxtitle = new Field($value);
            $oAttr->save();
        }
    }

    public function setObject2Attribute($i, $attr, $value)
    {
        $oObject2Attribute = oxNew(BaseModel::class);
        $oObject2Attribute->init("oxobject2attribute");

        if (!$oObject2Attribute->load(md5($i . md5($attr)))) {
            $oObject2Attribute->setId(md5($i . md5($attr)));
        }

        $oObject2Attribute->oxobject2attribute__oxobjectid = new Field(md5($i));
        $oObject2Attribute->oxobject2attribute__oxattrid = new Field(md5($attr));
        $oObject2Attribute->oxobject2attribute__oxvalue = new Field($value);
        $oObject2Attribute->save();
    }

    public function importImages($_sThisImportCSV, $_sImportStart, $_sImportCount)
    {
        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            $picturePath = getShopBasePath() . "/out/pictures/master/product/";
            $i = 0;
            $status = array();

            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[] = $jImportData;

                for ($c = 0; $c < $_sImportCount; $c++) {
                    $d = $c + $_sImportStart;

                    $_sThisTarPic = $array[$i][$d];
                    $_sThisDestPic = $picturePath . $c . "/csr_" . basename($array[$i][$d]);

                    if (!file_exists($_sThisDestPic)) {
                        if (!copy($_sThisTarPic, $_sThisDestPic)) {
                            // throw new Exception($i . ". Bild: " . $_sThisTarPic . " konnte nicht nach " . $_sThisDestPic . " kopiert werden");
                            $status['error']++;
                        } else {
                            $status['success']++;
                        }
                    } else {
                        // throw new Exception($i . ". Bild: " . $_sThisDestPic . " existiert bereits");
                        $status['exist']++;
                    }
                }
                $i++;
            }
        } else {
            throw new Exception("Datei konnte nicht gelesen werden.");
        }
        return $status;
    }

    public function getImporter($importid)
    {
        $import = '<form method="post" class="uploader-form" name="uploadFile-' . $importid . '" enctype="multipart/form-data">
					<div class="box">
						<input type="hidden" name="UploadPath" value="' . $importid . '">
						<input type="file" name="datei" id="file-' . $importid . '" class="inputfile inputfile-2" data-multiple-caption="{count} files selected" multiple />
						<label for="file-' . $importid . '"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Datei ausw&auml;hlen</span></label>
						<button class="inputButton">Upload</button>
					</div>
				   </form>';
        return $import;
    }

    public function setImportFile($filedata, $uploaddata)
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisUploadPath = getShopBasePath() . $_sThisImportConfig->getConfigParam($uploaddata['UploadPath']) . $filedata["datei"]["name"];
        $move = move_uploaded_file($filedata['datei']['tmp_name'], $_sThisUploadPath);
        if ($move) {
            echo "<span class='message topbox'>Datei wurde erfolgreich hochgeladen</span>";
        } else {
            echo "<span class='alert topbox'>Datei konnte nicht hochgeladen</span>.";
        }
    }

    private static function getAllCSRArticleIDs()
    {
        $query = "SELECT `oxid` FROM `oxarticles` WHERE `oxartnum` LIKE \"CSR-%\";";
        $resultSet = DatabaseProvider::getDb()->select($query);

        // Get the Result
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();
                $ids[md5($row[0])] = $row[0];
                $resultSet->fetchRow();
            }
        }
        return $ids;
    }

    private static function deleteOldArticles(array $articles)
    {
        $i = 0;
        foreach ($articles as $item) {
            $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $check = $product->delete($item);
            hd_import::setLog("delete", "Artikel $item gelöscht $check");
            $i++;
        }
        return $i;
    }
}

?>