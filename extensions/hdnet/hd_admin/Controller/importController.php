<?php

namespace HDNET\hdadmin\Controller;

use Exception;
use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class importController extends AdminController
{
    protected $newArticle = 0;

    protected $editArticle = 0;

    private QueryBuilderFactoryInterface $queryBuilderFactory;

    public function __constructor(QueryBuilderFactoryInterface $queryBuilderFactory): void
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @throws Exception
     */
    public function importArticlesAction(): void
    {
        $articleController = oxNew(articleController::class);
        $_sThisImportCSV = $this->getCSV("HD_IMPORT_CSR_ARTICLE_PATH", "HD_CSV_CSR_ARTICLE");

        if(!file_exists($_sThisImportCSV)) {
            echo "File $_sThisImportCSV ist nicht vorhanden!";
            return;
        }

        $articleData = $this->getDataFromCSV($_sThisImportCSV);

        try {
            $articles = $articleController->getAllCSRArticleIDs();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        foreach($articleData as $item) {
            try {
                $article = $articleController->importArticle($item);
                unset($articles[\md5($article['article'])]);

                if ($article['count'] === 1) {
                    $this->newArticle++;
                } else {
                    $this->editArticle++;
                }
            } catch (Exception $e) {
                $this->setLog("article", "$e");
            }
        }
        $oldArticles = $articleController->deleteOldArticles($articles);
        echo "Es wurden $this->newArticle Artikel angelegt und $this->editArticle bearbeitet. $oldArticles alte Artikel wurden gelöscht.";
    }

    public function importImagesAction(): void
    {
        $_sThisImportCSV = $this->getCSV("HD_IMPORT_CSR_ARTICLE_PATH", "HD_CSV_CSR_ARTICLE");
        try {
            $imageController = oxNew(imageController::class);
            $status = $imageController->importImages($_sThisImportCSV);
        } catch (Exception $e) {
            $this->log('pictures', $e->getMessage());
        }
        echo "Es wurden " . $status['succes'] . " Bilder heruntergladen, " . $status['exist'] . " Bilder waren bereits vorhanden und " . $status['error'] . " Bilder konnten nicht herunter geladen werden.";
    }

    public function importStockAction(): void
    {
        $_sThisImportStockCSV = $this->getCSV("HD_IMPORT_CSR_STOCK_PATH", "HD_CSV_CSR_STOCK");

        if (($jImportObject = fopen($_sThisImportStockCSV, "r")) !== FALSE) {
            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[] = $jImportData;
            }

            \array_shift($array);

            foreach($array as $item) {
                $product = oxNew(Article::class);
                $_sThisArtID = md5($item[0] . $item[1] . $item[2]);

                if (!$product->load($_sThisArtID)) {
                    $_sThisNotFound++;
                } else {
                    $_sThisEdit++;
                    $product->oxarticles__oxstock = new Field ($item[3]);
                    $product->save();
                }
            }
            $this->setLog("stock", "Bearbeitete Artikel: $_sThisEdit - $_sThisNotFound Artikel nicht gefunden");
            echo "Bearbeitete Artikel: $_sThisEdit - $_sThisNotFound Artikel nicht gefunden";
        } else {
            echo "Keine Datei ($_sThisImportStockCSV) zum öffnen gefunden";
        }
    }

    protected function getCSV(string $path, string $file): string
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        return getShopBasePath() . $_sThisImportConfig->getConfigParam($path) . $_sThisImportConfig->getConfigParam($file);
    }

    private function getDataFromCSV($csv): array
    {
        $array = [];
        if (($jImportObject = fopen($csv, "r")) !== FALSE) {
            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[] = $jImportData;
            }
            \array_shift($array);
        }
        return $array;
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
}