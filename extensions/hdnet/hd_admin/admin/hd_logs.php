<?php
namespace HDNET\hdadmin\admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;

/**
 * Jumbo View Class
 *
 * @author Christopher Olhoeft
 */
class hd_logs extends AdminController
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'hd_logs.tpl';

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     *
     * @return string
     */
    public function getLogData($log)
    {
        switch ($log) {
            case("order"):
                $output = '../log/hd_admin/order.log';
                break;
            case("article"):
                $output = '../log/hd_admin/article.log';
                break;
            case("picture"):
                $output = '../log/hd_admin/picture.log';
                break;
            case("stock"):
                $output = '../log/hd_admin/stock.log';
                break;
            case("delete"):
                $output = '../log/hd_admin/delete.log';
                break;
            case("error"):
                $output = '../log/oxideshop.log';
                break;
            case("version"):
                $output = ' ../modules/hdnet/hd_admin/version.txt';
                break;
        }
        return file_get_contents($output, true);
    }
}

?>
