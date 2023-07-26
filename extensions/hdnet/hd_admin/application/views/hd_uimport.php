<?php

namespace HDNET\hdadmin\application\views;

use hdnet\hd_admin\admin\hd_import;
use oxUBase;

/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */
class hd_uimport extends oxUBase
{

    protected $_sThisTemplate = 'hd_uimport.tpl';

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    public function importUserData()
    {
        $import = hd_import::setImportUserCSV();
    }
}