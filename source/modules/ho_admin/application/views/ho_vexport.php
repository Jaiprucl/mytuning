<?php
/** 
 * ho.Systeme View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_vexport extends oxUBase {

	protected $_sThisTemplate = 'ho_vexport.tpl';
	
	protected $_sThisExportEdit = true;
	
	public function render()
    {
        parent::render();
        return $this->_sThisTemplate;
    }

	public function createExportFile() {
		$export = ho_export::createCSV($this->_sThisExportEdit);
	
		if($export) {
			return "Bestellungen wurden erfogreich exportiert!";
		}
		else {
			if(count(ho_export::getOrderList()) == 0){
				return "Keine Bestellungen vorhanden";
			}
			else {
				return "Fehler";
			}
		}
	}
}