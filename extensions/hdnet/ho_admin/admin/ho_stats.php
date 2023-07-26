<?php
namespace hdnet\ho_admin\admin;

use oxAdminView;
use oxDb;

/**
 * ho.Systeme View Class
 *
 * @author Christopher Olhoeft
 */
class ho_stats extends oxAdminView
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'ho_stats.tpl';

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    public function getOrderToday()
    {
        $sQ = "SELECT COUNT(*) from `oxorder` where OXORDERDATE >= SUBDATE(NOW(), INTERVAL 1 DAY)";
        return oxDb::getDb()->getOne($sQ);
    }

    public function getOrderYesterday()
    {
        $sQ = "SELECT (*) from `oxorder` where OXORDERDATE >= SUBDATE(NOW(), INTERVAL 2 DAY)";
        return oxDb::getDb()->getOne($sQ);
    }

    public function getOrderWeek()
    {
        $sQ = "SELECT (*) from `oxorder` where OXORDERDATE >= SUBDATE(NOW(), INTERVAL 7 DAY)";
        return oxDb::getDb()->getOne($sQ);
    }

    public function getOrderMonth()
    {
        $sQ = "SELECT (*) from `oxorder` where OXORDERDATE >= SUBDATE(NOW(), INTERVAL 30 DAY)";
        return oxDb::getDb()->getOne($sQ);
    }
}

?>
