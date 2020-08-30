<?php
/** 
 * ho View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_search extends ho_search_parent {
    public function getAlist($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        // sets active page
        $this->iActPage = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgNr');
        $this->iActPage = ($this->iActPage < 0) ? 0 : $this->iActPage;

        // load only articles which we show on screen
        //setting default values to avoid possible errors showing article list
        $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->setSqlLimit($iNrofCatArticles * $this->iActPage, $iNrofCatArticles);

        $sSelect = $this->_getSearchSelectHO($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $sSortBy);
        if ($sSelect) {
            $oArtList->selectString($sSelect);
        }

        return $oArtList;
    }

    /*
    * @param string $sSearchParamForQuery       query parameter
    * @param string $sInitialSearchCat          initial category to search in
    * @param string $sInitialSearchVendor       initial vendor to search for
    * @param string $sInitialSearchManufacturer initial Manufacturer to search for
    * @param string $sSortBy                    sort by
    *
    * @return string
    */
   protected function _getSearchSelectHO($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
   {
       if (!$sSearchParamForQuery && !$sInitialSearchCat && !$sInitialSearchVendor && !$sInitialSearchManufacturer) {
           //no search string
           return null;
       }

       $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

       // performance
       if ($sInitialSearchCat) {
           // lets search this category - is no such category - skip all other code
           $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
           $sCatTable = $oCategory->getViewName();

           $sQ = "select 1 from $sCatTable where $sCatTable.oxid = " . $oDb->quote($sInitialSearchCat) . " ";
           $sQ .= "and " . $oCategory->getSqlActiveSnippet();
           if (!$oDb->getOne($sQ)) {
               return;
           }
       }

       // performance:
       if ($sInitialSearchVendor) {
           // lets search this vendor - if no such vendor - skip all other code
           $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
           $sVndTable = $oVendor->getViewName();

           $sQ = "select 1 from $sVndTable where $sVndTable.oxid = " . $oDb->quote($sInitialSearchVendor) . " ";
           $sQ .= "and " . $oVendor->getSqlActiveSnippet();
           if (!$oDb->getOne($sQ)) {
               return;
           }
       }

       // performance:
       if ($sInitialSearchManufacturer) {
           // lets search this Manufacturer - if no such Manufacturer - skip all other code
           $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
           $sManTable = $oManufacturer->getViewName();

           $sQ = "select 1 from $sManTable where $sManTable.oxid = " . $oDb->quote($sInitialSearchManufacturer) . " ";
           $sQ .= "and " . $oManufacturer->getSqlActiveSnippet();
           if (!$oDb->getOne($sQ)) {
               return;
           }
       }

       $sWhere = null;
       if ($sSearchParamForQuery) {
           $sWhere = $this->_getWhere($sSearchParamForQuery);
       }

       $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
       $sArticleTable = $oArticle->getViewName();
       $sO2CView = getViewName('oxobject2category');

       $sSelectFields = $oArticle->getSelectFields();

       // longdesc field now is kept on different table
       $sDescJoin = $this->getDescriptionJoin($sArticleTable);

       //select articles
       $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} {$sDescJoin} where ";

       // must be additional conditions in select if searching in category
       if ($sInitialSearchCat) {
           $sCatView = getViewName('oxcategories', $this->_iLanguage);
           $sInitialSearchCatQuoted = $oDb->quote($sInitialSearchCat);
           $sSelectCat = "select oxid from {$sCatView} where oxid = $sInitialSearchCatQuoted and (oxpricefrom != '0' or oxpriceto != 0)";
           if ($oDb->getOne($sSelectCat)) {
               $sSelect = "select {$sSelectFields}, {$sArticleTable}.oxtimestamp from {$sArticleTable} $sDescJoin " .
                          "where {$sArticleTable}.oxid in ( select {$sArticleTable}.oxid as id from {$sArticleTable}, {$sO2CView} as oxobject2category, {$sCatView} as oxcategories " .
                          "where (oxobject2category.oxcatnid=$sInitialSearchCatQuoted and oxobject2category.oxobjectid={$sArticleTable}.oxid) or (oxcategories.oxid=$sInitialSearchCatQuoted and {$sArticleTable}.oxprice >= oxcategories.oxpricefrom and
                           {$sArticleTable}.oxprice <= oxcategories.oxpriceto )) and ";
           } else {
               $sSelect = "select {$sSelectFields} from {$sO2CView} as
                           oxobject2category, {$sArticleTable} {$sDescJoin} where oxobject2category.oxcatnid=$sInitialSearchCatQuoted and
                           oxobject2category.oxobjectid={$sArticleTable}.oxid and ";
           }
       }

       $sSelect .= $oArticle->getSqlActiveSnippet();
       $sSelect .= " and {$sArticleTable}.oxparentid = '' and {$sArticleTable}.oxissearch = 1 ";

       if ($sInitialSearchVendor) {
           $sSelect .= " and {$sArticleTable}.oxvendorid = " . $oDb->quote($sInitialSearchVendor) . " ";
       }

       if ($sInitialSearchManufacturer) {
           $sSelect .= " and {$sArticleTable}.oxmanufacturerid = " . $oDb->quote($sInitialSearchManufacturer) . " ";
       }

       $sSelect .= $sWhere;

       if ($sSortBy) {
           $sSelect .= " order by {$sSortBy} ";
       }

       return $sSelect;
   }
}


