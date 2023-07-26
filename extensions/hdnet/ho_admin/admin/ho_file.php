<?php
/**
 *
 *
 * @author Christopher Olhoeft
 */
namespace hdnet\ho_admin\admin;
use oxAdminView;

ini_set('max_execution_time', 150);

class ho_file extends oxAdminView
{

    protected $_jThisPath = "../";

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'ho_file.tpl';

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
        $_sJumboFilePath = "../" . $_sFileUploadPath . "/" . $_FILES['jFile']['name'];
        $move = move_uploaded_file($_FILES['jFile']['tmp_name'], $_sJumboFilePath);
        if ($move) {
            return true;
        } else {
            return false;
        }
    }

    public function listFolderFilesSub($dir)
    {
        $ffs = scandir($dir);
        $out .= '<ul>';
        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..') {
                if (!is_dir($dir . '/' . $ff)) {
                    $out .= '<li><span class="file"><a href="../modules/ho_admin/file.php?get=' . ltrim($dir . '/' . $ff, './') . '">' . $ff . '</a><span>';
                } else {
                    $out .= '<li  class="closed" style="background:none;"><span class="folder"><b>' . $ff . '</b></span>';
                }
                if (is_dir($dir . '/' . $ff))
                    $out .= $this->listFolderFiles($dir . '/' . $ff);
                $out .= '</li>';
            }
        }
        $out .= '</ul>';
        return $out;
    }

    public function listFolderFiles($dir)
    {
        $ffs = scandir($dir);
        $out .= '<ul id="main" class="filetree">';
        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..') {
                if (!is_dir($dir . '/' . $ff)) {
                    $out .= '<li><span class="file"><a href="../modules/ho_admin/file.php?get=' . ltrim($dir . '/' . $ff, './') . '">' . $ff . '</a><span>';
                } else {
                    $out .= '<li class="closed" style="background:none;"><span class="folder">' . $ff . '</span>';
                }
                if (is_dir($dir . '/' . $ff))
                    $out .= $this->listFolderFilesSub($dir . '/' . $ff);
                $out .= '</li>';
            }
        }
        $out .= '</ul>';
        return $out;
    }
}

?>
