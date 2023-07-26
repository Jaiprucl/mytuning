<?php
/**
 *    This file is part of jumbo
 *
 * @link      https://www.jumbo-discount.de/
 * @author    Christopher Olhoeft
 */

namespace hdnet\ho_admin\admin;

use hdnet\ho_admin\admin\ho_seo;
use oxConfig;
use oxDb;
use oxSeoEncoder;

/**
 * Base seo config class
 */
class ho_lpactions_seo extends ho_seo
{
    /**
     * Executes parent method parent::render(),
     * and returns name of template file
     * "object_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        return 'ho_seo.tpl';
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        $aSeoData = oxConfig::getParameter('aSeoData');
        if (!$this->existEntry()) {
            $sQ = "INSERT INTO `oxseo` (OXOBJECTID, OXIDENT, OXSHOPID, OXLANG, OXSTDURL, OXSEOURL, OXTYPE, OXFIXED, OXEXPIRED, OXPARAMS, OXTIMESTAMP ) 
			VALUES ('" . $this->getEditObjectId() . "',
					'" . md5(strtolower($aSeoData['oxseourl'])) . "',
					'" . $this->getConfig()->getShopId() . "', 
					'" . $this->getEditLang() . "', 
					'index.php?cl=landingpages&lp=" . $this->getEditObjectId() . "', 
					'" . $aSeoData['oxseourl'] . "',
					'dynamic',
					'0',
					'0',
					'',
					CURRENT_TIMESTAMP)";
            $sSql = oxDb::getDb()->Execute($sQ);
        } else {
            $sQ = "UPDATE `oxseo` SET oxseourl = '" . $aSeoData['oxseourl'] . "', oxident = '" . md5(strtolower($aSeoData['oxseourl'])) . "' WHERE oxobjectid = '" . $this->getEditObjectId() . "' LIMIT 1;";
            $sSql = oxDb::getDb()->Execute($sQ);
        }
    }

    /**
     * Returns id of object which must be saved
     *
     * @return string
     */
    protected function _getSaveObjectId()
    {
        return $this->getEditObjectId();
    }

    /**
     * Returns object seo data
     *
     * @param string $sMetaType meta data type (oxkeywords/oxdescription)
     *
     * @return string
     */
    public function getEntryMetaData($sMetaType)
    {
        return $this->_getEncoder()->getMetaData($this->getEditObjectId(), $sMetaType, $this->getConfig()->getShopId(), $this->getEditLang());
    }

    /**
     * Returns TRUE if current seo entry has fixed state
     *
     * @return bool
     */
    public function isEntryFixed()
    {
        $iLang = (int)$this->getEditLang();
        $iShopId = $this->getConfig()->getShopId();

        $sQ = "select oxfixed from oxseo where
                   oxseo.oxobjectid = " . oxDb::getDb()->quote($this->getEditObjectId()) . " and
                   oxseo.oxshopid = '{$iShopId}' and oxseo.oxlang = {$iLang} and oxparams = '' ";
        return (bool)oxDb::getDb()->getOne($sQ, false, false);
    }

    public function existEntry()
    {
        $iLang = (int)$this->getEditLang();
        $iShopId = $this->getConfig()->getShopId();

        $sQ = "select * from oxseo where
                   oxseo.oxobjectid = " . oxDb::getDb()->quote($this->getEditObjectId()) . " and
                   oxseo.oxshopid = '{$iShopId}' and oxseo.oxlang = {$iLang} and oxparams = '' ";
        return (bool)oxDb::getDb()->getOne($sQ, false, false);
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
    }

    /**
     * Returns edit language id
     *
     * @return int
     */
    public function getEditLang()
    {
        return $this->_iEditLang;
    }

    /**
     * Returns alternative seo entry id
     *
     * @return null
     */
    protected function _getAltSeoEntryId()
    {
    }

    /**
     * Returns seo entry type
     *
     * @return string
     */
    protected function _getSeoEntryType()
    {
        return $this->_getType();
    }

    /**
     * Processes parameter before writing to db
     *
     * @param string $sParam parameter to process
     *
     * @return string
     */
    public function processParam($sParam)
    {
        return $sParam;
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoder
     */
    protected function _getEncoder()
    {
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
    }

    /**
     * Returns true if SEO object id has suffix enabled. Default is FALSE
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        return false;
    }

    /**
     * Returns TRUE if seo object supports suffixes. Default is FALSE
     *
     * @return bool
     */
    public function isSuffixSupported()
    {
        return false;
    }

    /**
     * Returns FALSE, as this view does not support category selector
     *
     * @return bool
     */
    public function showCatSelect()
    {
        return false;
    }

    /**
     * Returns FALSE, as this view does not support active selection type
     *
     * @return bool
     */
    public function getActCatType()
    {
        return false;
    }

}
