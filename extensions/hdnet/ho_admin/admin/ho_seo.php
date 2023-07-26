<?php
/**
 *    This file is part of jumbo
 *
 * @link      https://www.jumbo-discount.de/
 * @author    Christopher Olhoeft
 */

namespace hdnet\ho_admin\admin;

use hdnet\ho_admin\admin\ho_admindetails;
use oxDb;

/**
 * Content seo config class
 */
class ho_seo extends ho_admindetails
{
    public function getSeoUri()
    {

        $iShopId = $this->getConfig()->getShopId();

        $sQ = "select oxseo.oxseourl from oxseo where
                   oxseo.oxobjectid = " . oxDb::getDb()->quote($this->getEditObjectId()) . " and
                   oxseo.oxshopid = '" . $iShopId . "' and oxseo.oxlang = " . $this->getEditLang();
        return oxDb::getDb()->getOne($sQ, false, false);
    }
}
