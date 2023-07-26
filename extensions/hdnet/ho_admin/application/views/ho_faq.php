<?php

namespace hdnet\ho_admin\application\views;
use oxConfig;
use oxDb;
use oxUBase;

/**
 * ho.Systeme View Class
 *
 * @author Christopher Olhoeft
 */
class ho_faq extends oxUBase
{

    protected $_sThisTemplate = 'faq.tpl';

    public function render()
    {
        if (oxConfig::getParameter('showexceptionpage') == '1') {
            return 'message/exception.tpl';
        }
        parent::render();

        return $this->_sThisTemplate;
    }

    public function getFaqGroups()
    {
        $sQ = "SELECT jdgroup FROM `jdfaqgroup`;";
        $rs = oxDb::getDb(true)->Execute($sQ);

        while (!$rs->EOF) {
            $sThisArray[] = array('Group' => $rs->fields[0]);
            $rs->MoveNext();
        }
        return $sThisArray;
    }

    public function getFaq($faq)
    {
        $sQ = "SELECT * FROM `jdfaq` WHERE `JDGROUP` = '" . $faq . "';";
        $rs = oxDb::getDb(true)->Execute($sQ);

        while (!$rs->EOF) {
            $sThisArray[] = array(
                'Group' => $rs->fields[1],
                'Question' => $rs->fields[2],
                'Answer' => $rs->fields[3],
            );
            $rs->MoveNext();
        }
        return $sThisArray;
    }
}
