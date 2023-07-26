<?php

namespace HDNET\hdadmin\application\views;

use HDNET\hdadmin\admin\hd_import;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Config;

/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */
class hd_picture extends FrontendController
{

    protected $_sThisTemplate = 'hd_picture.tpl';

    public function render()
    {
        if (Config::getParameter('showexceptionpage') == '1') {
            return 'message/exception.tpl';
        }
        parent::render();

        return $this->_sThisTemplate;
    }

    public function importImageData()
    {
        $import = hd_import::getImportImagesCSV();
    }
}