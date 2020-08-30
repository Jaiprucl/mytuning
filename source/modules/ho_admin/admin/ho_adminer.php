<?php
/** 
 * View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_adminer extends oxAdminView {
	/**
	 * Current class template name.
	 * @var string
	 */
	protected $_sThisTemplate = 'ho_adminer.tpl';
	
	public function render() {
		parent::render();

		return $this->_sThisTemplate;
	}
	
	/**
	 * 
	 * @var string
	 */
}
?>
