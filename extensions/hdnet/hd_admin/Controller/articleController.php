<?php

namespace HDNET\hdadmin\Controller;

use Exception;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class articleController extends importController
{
    /**
     * @throws Exception
     */
    public function importArticle($array): bool|array
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

        $parent = \explode("-", $article['oxartnum']);

        if(isset($parent[2])) {
            $parentId = $parent[0] . "-" . $parent[1];
        }

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
            ->select('oxid, oxartnum')
            ->from('oxarticles')
            ->where('oxartnum LIKE :artnum')
            ->setParameters([
                'artnum' => 'CSR-%',
            ]);

        $blocksData = $queryBuilder->execute();
        $articleData = $blocksData->fetchAll();
        foreach($articleData as $item){
            $articleIds[\md5($item['oxid'])] = [
                'oxid' => $item['oxid'],
                'oxartnum' => $item['oxartnum']
            ];
        }
        return $articleIds;
    }

    /**
     * @throws Exception
     */
    public function deleteOldArticles(array $articles): int
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