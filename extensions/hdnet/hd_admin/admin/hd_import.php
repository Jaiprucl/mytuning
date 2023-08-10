<?php
/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */

namespace HDNET\hdadmin\admin;

use Exception;
use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
    protected string $_sThisPicturePath = '/out/pictures/master/product/';
    /**
     * @var QueryBuilderFactoryInterface
     */
    protected QueryBuilderFactoryInterface $queryBuilderFactory;

    /**
     *
     * @return string
     */
    public function render(): string
    {
        parent::render();

        if ($_FILES['datei']) {
            $this->setImportFile($_FILES, $_POST);
        }
        return $this->_sThisTemplate;
    }

    public function __constructor(QueryBuilderFactoryInterface $queryBuilderFactory): void
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @throws Exception
     */
    public function setImportArticleCSV(): void
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");

        try {
            $articles = $this->getAllCSRArticleIDs();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        $new = 0;
        $edit = 0;

        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            while ($jImportData = fgetcsv($jImportObject, 15000, chr(59), chr(0))) {
                if ($i > 0) {
                    try {
                        $article = $this->setImportArticle($jImportData);
                        if ($article['count'] === 1) {
                            $new++;
                        } else {
                            $edit++;
                        }
                    } catch (Exception $e) {
                        $this->setLog("article", "$e");
                    }
                }
                unset($articles[\md5($article['article'])]);
                $i++;
            }
            $oldArticles = $this->deleteOldArticles($articles);
            echo "Es wurden $new Artikel angelegt und $edit bearbeitet. $oldArticles alte Artikel wurden gelöscht.";
        }
    }

    /**
     * @throws Exception
     */
    public function setImportArticle($array): bool|array
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

        if($article['oxprice'] === '0.00') {
            return true;
        }

        $status['count'] = 0;

        $product = oxNew(Article::class);

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
        $product->oxarticles__oxweight = new Field($this->getShippingValue($article['oxshippingcat']));
        $product->oxarticles__oxpic1 = new Field($article['oxpic1']);
        $product->oxarticles__oxpic2 = new Field($article['oxpic2']);
        $product->oxarticles__oxpic3 = new Field($article['oxpic3']);
        $product->oxarticles__oxpic4 = new Field($article['oxpic4']);
        $product->oxarticles__oxpic5 = new Field($article['oxpic5']);
        $product->oxarticles__oxpic6 = new Field($article['oxpic6']);
        $product->save();

        # Set Longdescription
        $this->setLongDesc($article['oxid'], $article['oxlongdesc']);

        # Versandkategorie
        $this->checkAttributeAndSet("Versandkategorie");
        $this->setObject2Attribute($article['oxid'], "Versandkategorie", $article['oxshippingcat']);

        $status['article'] = $article['oxid'];
        return $status;
    }

    public function setImportStockCSV(): void
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

                    $product = oxNew(Article::class);
                    $_sThisArtID = $array[$i][0] . $array[$i][1] . $array[$i][2];

                    if (!$product->load(md5($_sThisArtID))) {
                        $_sThisNotFound++;
                    } else {
                        $_sThisEdit++;
                        $product->oxarticles__oxstock = new Field ($array[$i][3]);
                        $product->save();
                    }
                }
                $i++;
            }
            fclose($jImportObject);
            $this->setLog("stock", "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden");
            echo "Bearbeitete Artikel:" . $_sThisEdit . " - " . $_sThisNotFound . " Artikel nicht gefunden";
        } else {
            echo "Keine Datei zum öffnen gefunden";
        }
    }

    public function setImportImagesCSV(): void
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisImportCSV = getShopBasePath() . $_sThisImportConfig->getConfigParam("HD_IMPORT_CSR_ARTICLE_PATH") . $_sThisImportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");

        try {
            $status = $this->importImages($_sThisImportCSV);
        } catch (Exception $e) {
            $this->log('pictures', $e->getMessage());
        }
        echo "Es wurden " . $status['succes'] . " Bilder heruntergladen, " . $status['exist'] . " Bilder waren bereits vorhanden und " . $status['error'] . " Bilder konnten nicht herunter geladen werden.";
    }

    public function setShippingID(): void
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

    public function getCsvData($csv): string
    {
        $_sThisExportConfig = Registry::get("oxConfig");
        $_sThisExportPath = getShopBasePath() . $_sThisExportConfig->getConfigParam("HD_IMPORT_PATH");

        switch ($csv) {
            case ("picture"):
            case("article"):
                $_sThisExportFile = $_sThisExportConfig->getConfigParam("HD_CSV_CSR_ARTICLE");
                break;
            case("user"):
                $_sThisExportFile = $_sThisExportConfig->getConfigParam("HD_CSV_CSR_USER");
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

    public function url_check($url): bool|int
    {
        $hdrs = @get_headers($url);
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false;
    }

    public function getShippingValue($value): int
    {
        switch ($value) {
            case ("0 EUR"):
            case ("7.7 EUR"):
            case ("6.64 EUR"):
            case ("IngoNoak Kat3"):
            case("Shop Kat 5"):
                $shoppingValue = 1;
                break;
            case ("Shop Kat 3"):
            case ("Kategorie 2"):
            case ("1 - GLS"):
            case ("1 - DHL Paket"):
            case("Shop Kat 4"):
                $shoppingValue = 100;
                break;
            case ("29.93 EUR"):
            case ("Shop Kat 2"):
            case ("4 - DHL Sperrgut"):
            case("3 - Spedition"):
                $shoppingValue = 1000000;
                break;
            case ("Shop Kat 0"):
            case("IngoNoak Kat5"):
                $shoppingValue = 10000;
                break;
            case("Shop Kat 1"):
                $shoppingValue = 100000000;
                break;
            case("Versandkostenfrei in alle Länder"):
                $shoppingValue = 0;
                break;
            default:
                $shoppingValue = 999999999;
                $this->setLog("shipping", "Konnte $value nicht finden");
        }
        return $shoppingValue;
    }

    public function setLongDesc($id, $desc): void
    {
        $oArtExt = oxNew(MultiLanguageModel::class);
        $oArtExt->init('oxartextends');
        $oArtExt->setId(md5($id));
        $oArtExt->oxartextends__oxlongdesc = new Field($desc);
        $oArtExt->save();
    }

    /**
     * @throws Exception
     */
    public function checkAttributeAndSet($value): void
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

    /**
     * @throws Exception
     */
    public function setObject2Attribute($i, $attr, $value): void
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

    public function importImages($_sThisImportCSV): array
    {
        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            $picturePath = getShopBasePath() . "out/pictures/master/product/";
            $status = [];

            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[$jImportData[0]][1] = $jImportData[6];
                $array[$jImportData[0]][2] = $jImportData[7];
                $array[$jImportData[0]][3] = $jImportData[8];
                $array[$jImportData[0]][4] = $jImportData[9];
                $array[$jImportData[0]][5] = $jImportData[10];
                $array[$jImportData[0]][6] = $jImportData[11];
            }

            \array_shift($array);

            foreach($array as  $item) {
                foreach($item as $pic => $key) {
                    $_sThisTarPic = $key;
                    $_sThisDestPic = $picturePath . $pic . "/csr_" . basename($key);

                    if($_sThisTarPic === '') {
                        continue;
                    }

                    if (!file_exists($_sThisDestPic)) {
                        if (!copy($_sThisTarPic, $_sThisDestPic)) {
                            $status['error']++;
                        } else {
                            $status['success']++;
                        }
                    } else {
                        $status['exist']++;
                    }
                }
            }
        }
        return $status;
    }

    public function getImporter($importid): string
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

    public function setImportFile($filedata, $uploaddata): void
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisUploadPath = getShopBasePath() . $_sThisImportConfig->getConfigParam($uploaddata['UploadPath']) . $filedata["datei"]["name"];
        $move = move_uploaded_file($filedata['datei']['tmp_name'], $_sThisUploadPath);
        if ($move) {
            echo "<span class='message topbox'>Datei wurde erfolgreich hochgeladen</span>";
        } else {
            echo "<span class='alert topbox'>Datei konnte nicht hochgeladen werden</span>.";
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllCSRArticleIDs(): array|null
    {
        $articleIds = [];
        $container = ContainerFactory::getInstance()->getContainer();
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder
            ->select('oxid')
            ->from('oxarticles')
            ->where('oxartnum LIKE :artnum')
            ->setParameters([
                'artnum'    => 'CSR-%',
            ]);

        $blocksData = $queryBuilder->execute();
        $articleData = $blocksData->fetchAll();
        foreach($articleData as $item){
            $articleIds[\md5($item['oxid'])] = $item['oxid'];
        }
        return $articleIds;
    }

    /**
     * @throws Exception
     */
    private function deleteOldArticles(array $articles): int
    {
        $i = 0;
        if(!empty($articles)) {
            foreach ($articles as $item) {
                $product = oxNew(Article::class);
                $check = $product->delete($item['oxid']);
                $this->setLog("delete", "Artikel $item gelöscht $check");
                $i++;
            }
        }
        return $i;
    }
}