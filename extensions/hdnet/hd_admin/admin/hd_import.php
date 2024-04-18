<?php
/**
 * HDNET View Class
 *
 * @author Christopher Olhoeft
 */

namespace HDNET\hdadmin\admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

set_time_limit(180);

class hd_import extends AdminController
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'hd_import.tpl';


    /**
     * Current class template name.
     * @var string
     */
    protected string $_sThisPicturePath = '/out/pictures/master/product/';

    /**
     *
     * @return string
     */
    public function render(): string
    {
        parent::render();

        if ($_FILES['datei']) {
            $this->setImportFile($_FILES, $_POST);
        }
        return $this->_sThisTemplate;
    }

    public function setShippingID(): void
    {
        $query = "SELECT a.`oxid`, o.`oxvalue` FROM `oxarticles` AS a JOIN `oxobject2attribute` AS o ON a.`oxid` = o.`oxobjectid`";
        $resultSet = DatabaseProvider::getDb()->select($query);

        // Get the Result
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();
                $vID = intval(substr(str_replace(" ", "", $row[1]), -1));
                $sQ = "UPDATE `oxarticles` SET `oxweight` = " . $vID . " WHERE `oxid` = '" . $row[0] . "';";
                $result = DatabaseProvider::getDb()->execute($sQ);
                // hd_import::setLog("article", "Bearbeitete Artikel:" . $sQ);
                $resultSet->fetchRow();
            }
        }
    }

    public function getImporter($importid): string
    {
        $import = '<form method="post" class="uploader-form" name="uploadFile-' . $importid . '" enctype="multipart/form-data">
					<div class="box">
						<input type="hidden" name="UploadPath" value="' . $importid . '">
						<input type="file" name="datei" id="file-' . $importid . '" class="inputfile inputfile-2" data-multiple-caption="{count} files selected" multiple />
						<label for="file-' . $importid . '"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Datei ausw&auml;hlen</span></label>
						<button class="inputButton">Upload</button>
					</div>
				   </form>';
        return $import;
    }

    public function setImportFile($filedata, $uploaddata): void
    {
        $_sThisImportConfig = Registry::get("oxConfig");
        $_sThisUploadPath = getShopBasePath() . $_sThisImportConfig->getConfigParam($uploaddata['UploadPath']) . $filedata["datei"]["name"];
        $move = move_uploaded_file($filedata['datei']['tmp_name'], $_sThisUploadPath);
        if ($move) {
            echo "<span class='message topbox'>Datei wurde erfolgreich hochgeladen</span>";
        } else {
            echo "<span class='alert topbox'>Datei konnte nicht hochgeladen werden</span>.";
        }
    }
}