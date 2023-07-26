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
        $import = hd_import::setImportArticleCSV();
        return $import;
    }

    public function importRiegerArticleData()
    {
        $import = hd_import::setImportRiegerArticleCSV();
        return $import;
    }

    public function importFkArticleData()
    {
        $import = hd_import::setImportFkArticleCSV();
        return $import;
    }

    public function importUserData()
    {
        $import = hd_import::setImportUserCSV();
        return $import;
    }

    public function importPictureData()
    {
        $import = hd_import::setImportImagesCSV();
        return $import;
    }

    public function importStockData()
    {
        $import = hd_import::setImportStockCSV();
        return $import;
    }

    public function importRiegerPictureData()
    {
        $import = hd_import::setImportRiegerImagesCSV();
        return $import;
    }

    public function importFkPictureData()
    {
        $import = hd_import::setImportFkImagesCSV();
        return $import;
    }

    public function importShipData()
    {
        $import = hd_import::setShippingID();
        return $import;
    }
}