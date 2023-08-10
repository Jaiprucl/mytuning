<?php

namespace HDNET\hdadmin\application\views;

use OxidEsales\Eshop\Application\Controller\FrontendController;

/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */
class hd_vexport extends FrontendController
{

    protected $_sThisTemplate = 'hd_vexport.tpl';

    protected $_sThisExportEdit = true;

    public function render()
    {
        parent::render();
        return $this->_sThisTemplate;
    }

    public function createExportFile()
    {
        $export = hd_export::createCSV($this->_sThisExportEdit);

        if ($export) {
            return "Bestellungen wurden erfolgreich exportiert!";
        } else {
            if (count(hd_export::getOrderList()) == 0) {
                return "Keine Bestellungen vorhanden";
            } else {
                return "Fehler";
            }
        }
    }
}