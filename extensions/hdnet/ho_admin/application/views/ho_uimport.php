<?php

namespace hdnet\ho_admin\application\views;

use hdnet\ho_admin\admin\ho_import;
use oxUBase;

/**
 * ho.Systeme View Class
 *
 * @author Christopher Olhoeft
 */
class ho_uimport extends oxUBase
{

    protected $_sThisTemplate = 'ho_uimport.tpl';

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    public function importUserData()
    {
        $import = ho_import::setImportUserCSV();
    }
}