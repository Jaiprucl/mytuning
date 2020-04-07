<?php
/** 
 * Jumbo View Class
 * 
 * @author Christopher Olhoeft
 */
class ho_logs extends oxAdminView {
	/**
	 * Current class template name.
	 * @var string
	 */
	protected $_sThisTemplate = 'ho_logs.tpl';
	
	public function render() {
		parent::render();

		return $this->_sThisTemplate;
	}
	
	/**
	 * 
	 * @var string
	 */

	 public function getLogData($log) {
		switch($log) {
			case("order"): $output = '../log/ho_admin/order.log'; break;		
			case("article"): $output = '../log/ho_admin/article.log'; break;			
			case("picture"): $output = '../log/ho_admin/picture.log'; break;
			case("stock"): $output = '../log/ho_admin/stock.log'; break;
			case("error"): $output = '../log/oxideshop.log'; break;
			case("version"): $output = '../modules/ho_admin/version.txt'; break;
		}
		return file_get_contents( $output, true);
	 }
}
?>
