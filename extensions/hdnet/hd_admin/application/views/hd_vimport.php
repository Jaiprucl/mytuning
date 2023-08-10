<?php

namespace HDNET\hdadmin\application\views;

use HDNET\hdadmin\admin\hd_import;
use OxidEsales\Eshop\Application\Controller\FrontendController;

/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */
class hd_vimport extends FrontendController
{

    protected $_sThisTemplate = 'hd_vimport.tpl';

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    public function importArticleData()
    {
        $import = oxNew(hd_import::class);
        return $import->setImportArticleCSV();
    }

    public function importPictureData()
    {
        $import = oxNew(hd_import::class);
        return  $import->setImportImagesCSV();
    }

    public function importStockData()
    {
        $import = oxNew(hd_import::class);
        return $import->setImportStockCSV();
    }

    public function importShipData()
    {
        $import = oxNew(hd_import::class);
        return $import->setShippingID();
    }
}