<?php
namespace hdnet\ho_admin\admin;

use oxAdminView;
use oxDb;

/**
 *
 *
 * @author Christopher Olhoeft
 */
class ho_config extends oxAdminView
{

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'ho_config.tpl';

    /**
     *
     * @return string
     */

    public function render()
    {
        parent::render();

        if ($_POST['install']) {
            $install = $this->setupInstallion();
            if ($install) {
                echo "Installation war erfolgreich.";
            } else {
                echo "Installation ist fehlgeschlagen. <br>Fehler " . mysql_error();
            }
        }

        if ($_POST['saveSettings']) {
            echo "Installation beginnen";
        }

        return $this->_sThisTemplate;
    }

    /**
     *
     *
     * @return void
     */

    public function getTableConfig()
    {
        $sQ = "SHOW TABLES LIKE `ho_config`;";
        $sSql = oxDb::getDb()->Execute($sQ);

        if ($sSql) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     *
     * @return void
     */

    public function setupInstallion()
    {
        $sQ = "CREATE TABLE `ho_config`(
			   JID char(32) COLLATE latin1_general_ci NOT NULL,
			   JVARNAME VARCHAR(255) COLLATE latin1_general_ci NOT NULL,
			   JVARVALUE VARCHAR(255) COLLATE latin1_general_ci NOT NULL,
			   JTIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			   PRIMARY KEY (`JID`))ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
        $sSql = oxDb::getDb()->Execute($sQ);

        if ($sSql) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     *
     * @return void
     */

}

?>
