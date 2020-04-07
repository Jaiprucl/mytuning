<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_category extends oxUBase {

	protected $_sThisTemplate = 'ho_category.tpl';
	
	public function render(){
        if ( oxConfig::getParameter( 'showexceptionpage' ) == '1' ) {
            return 'message/exception.tpl';
        }
        parent::render();

        return $this->_sThisTemplate;
    }
	
	public function importCategoryData() {
		$import = ho_import::getImportCategoryCSV();
	}
}