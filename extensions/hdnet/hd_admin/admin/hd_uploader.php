<?php
namespace HDNET\hdadmin\admin;

use oxAdminView;

/**
 *
 *
 * @author Christopher Olhoeft
 */
class hd_uploader extends oxAdminView
{

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'hd_uploader.tpl';

    /**
     *
     * @return string
     */

    public function render()
    {
        parent::render();

        if ($_POST['uploadFile']) {
            $upload = $this->getFile($_POST['uploadPath']);
            echo ($upload) ? "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #ddffb2;'>Datei wurde hochgeladen!</div>" : "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #fcc4c4;'>Es ist ein Fehler aufgetreten!</div>";
        }

        return $this->_sThisTemplate;
    }

    /**
     *
     * @return string
     */

    public function getFile($_sFileUploadPath)
    {
        $_sJumboFilePath = $_sFileUploadPath . "/" . $_FILES['jFile']['name'];
        $move = move_uploaded_file($_FILES['jFile']['tmp_name'], $_sJumboFilePath);
        if ($move) {
            return true;
        } else {
            return false;
        }
    }

    public function getFileSystem()
    {
        return true;
    }
}

?>
