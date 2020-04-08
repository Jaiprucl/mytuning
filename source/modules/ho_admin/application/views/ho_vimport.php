<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_vimport extends oxUBase {

	protected $_sThisTemplate = 'ho_vimport.tpl';
	
	public function render(){
        parent::render();

        return $this->_sThisTemplate;
    }
	
	public function importArticleData() {
		$import = ho_import::setImportArticleCSV();
		return $import;
	}
	public function importUserData() {
		$import = ho_import::setImportUserCSV();
		return $import;
	}

	public function importPictureData() {
		$import = ho_import::setImportImagesCSV();
		return $import;
	}
	public function importStockData() {
		$import = ho_import::setImportStockCSV();
		return $import;
	}
	public function importShipData() {
		$import = ho_import::setShippingID();
		return $import;
	}
}