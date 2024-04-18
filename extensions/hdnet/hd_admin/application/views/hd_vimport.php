<?php

namespace HDNET\hdadmin\application\views;

use HDNET\hdadmin\admin\hd_import;
use HDNET\hdadmin\Controller\imageController;
use HDNET\hdadmin\Controller\importController;
use HDNET\hdadmin\Controller\stockController;
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
        $import = oxNew(importController::class);
        return $import->importArticlesAction();
    }

    public function importPictureData()
    {
        $import = oxNew(imageController::class);
        return $import->importImagesAction();
    }

    public function importStockData()
    {
        $import = oxNew(stockController::class);
        return $import->importStockAction();
    }

    public function importShipData()
    {
        $import = oxNew(hd_import::class);
        return $import->setShippingID();
    }
}