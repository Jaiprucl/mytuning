<?php

namespace hdnet\ho_admin\admin;
use ho_alist_parent;

/**
 * ho View Class
 *
 * @author Christopher Olhoeft
 */
class ho_alist extends ho_alist_parent
{
    public function getAlist($attributeFilter)
    {
        $config = $this->getConfig();
        $category = $this->getActiveCategory();

        $numberOfCategoryArticles = (int)$config->getConfigParam('iNrofCatArticles');
        $numberOfCategoryArticles = $numberOfCategoryArticles ? $numberOfCategoryArticles : 1;

        $baseLanguageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

        if (!empty($attributeFilter)) {
            $sessionFilter = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter');
            $sessionFilter[$category->oxcategories__oxid->value] = null;
            $sessionFilter[$category->oxcategories__oxid->value][$baseLanguageId] = array(md5("Kundennummer") => $attributeFilter);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('session_attrfilter', $sessionFilter);
        }

        $articleList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $articleList->setSqlLimit($numberOfCategoryArticles * $this->_getRequestPageNr(), $numberOfCategoryArticles);
        $articleList->setCustomSorting($this->getSortingSql($this->getSortIdent()));

        if ($category->isPriceCategory()) {
            $priceFrom = $category->oxcategories__oxpricefrom->value;
            $priceTo = $category->oxcategories__oxpriceto->value;

            $this->_iAllArtCnt = $articleList->loadPriceArticles($priceFrom, $priceTo, $category);
        } else {
            $sessionFilter = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter');
            // $activeCategoryId = $category->getId();
            $this->_iAllArtCnt = $articleList->loadCategoryArticles($category->oxcategories__oxid->value, $sessionFilter);
        }
        $this->_iCntPages = ceil($this->_iAllArtCnt / $numberOfCategoryArticles);

        return $articleList;
    }
}


