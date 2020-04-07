<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_picture extends oxUBase {

	protected $_sThisTemplate = 'ho_picture.tpl';
	
	public function render(){
        if ( oxConfig::getParameter( 'showexceptionpage' ) == '1' ) {
            return 'message/exception.tpl';
        }
        parent::render();

        return $this->_sThisTemplate;
    }
	
	public function importImageData() {
		$import = ho_import::getImportImagesCSV();
	}
}