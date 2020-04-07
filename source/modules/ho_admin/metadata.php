<?php
/**
 *    This file is part of Christopher Olhoeft
 *
 * @link      https://www.hosysteme.de/
 * @author    Christopher Olhoeft
 */
use OxidEsales\Eshop\Application\Controller\ArticleListController;
use OxidEsales\Eshop\Application\Controller\SearchController;
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           									=> 'ho_admin',
    'title'        									=> 'ho.Systeme Administration',
    'description'  									=> 'ho.Systeme Administrationstool',
    'thumbnail'    									=> 'ho.png',
    'version'      									=> '1.0.0',
    'author'       									=> 'Christopher Olhoeft',
    'url'          									=> 'https://www.hosysteme.de/',
    'email'        									=> 'olhoeft@hosysteme.de',
    'extend' => array(
		'ho_vexport'      						    => 'ho_admin/application/views/ho_vexport',
		'ho_vimport'      						    => 'ho_admin/application/views/ho_vimport',
		'ho_picture'      						    => 'ho_admin/application/views/ho_picture',
        ArticleListController::class                => 'ho_admin/admin/ho_alist',
        SearchController::class                     => 'ho_admin/admin/ho_search',
    ),
    'files' => array(
        'ho_file'      							    => 'ho_admin/admin/ho_file.php',
        'ho_export'     							=> 'ho_admin/admin/ho_export.php',
        'ho_import'     							=> 'ho_admin/admin/ho_import.php',
        'ho_logs'     							    => 'ho_admin/admin/ho_logs.php',
        'ho_admindetails'    					    => 'ho_admin/admin/ho_admindetails.php',
		'ho_vexport'     						    => 'ho_admin/application/views/ho_vexport.php',
		'ho_vimport'     						    => 'ho_admin/application/views/ho_vimport.php',
		'ho_picture'     						    => 'ho_admin/application/views/ho_picture.php',
    ),
    'templates' => array(
        'ho_file.tpl'  							    => 'ho_admin/out/admin/tpl/ho_file.tpl',
        'ho_export.tpl' 						    => 'ho_admin/out/admin/tpl/ho_export.tpl',
        'ho_import.tpl' 						    => 'ho_admin/out/admin/tpl/ho_import.tpl',
        'ho_logs.tpl' 							    => 'ho_admin/out/admin/tpl/ho_logs.tpl',
        'ho_vimport.tpl' 						    => 'ho_admin/out/tpl/ho_vimport.tpl',
        'ho_picture.tpl' 						    => 'ho_admin/out/tpl/ho_picture.tpl',
        'ho_vexport.tpl' 						    => 'ho_admin/out/tpl/ho_vexport.tpl',
    ),
	
	'blocks' => array(
        array('template' => 'actions_main.tpl', 'block'=>'admin_actions_main_form', 'file'=>'out/blocks/actions_main_block.tpl'),
    ),
	
	'settings' => array(
        array('group' => 'PATH', 'name' => 'HO_IMPORT_CSR_ARTICLE_PATH', 'type' => 'str',  'value' => 'modules/ho_admin/export/in/Artikel/'),
        array('group' => 'PATH', 'name' => 'HO_IMPORT_CSR_STOCK_PATH', 'type' => 'str',  'value' => 'modules/ho_admin/export/in/Bestand/'),
        array('group' => 'PATH', 'name' => 'HO_EXPORT_PATH', 'type' => 'str',  'value' => 'modules/ho_admin/export/out/'),
        array('group' => 'CSV', 'name' => 'HO_CSV_CSR_ARTICLE', 'type' => 'str',  'value' => 'exp_produkte.csv'),
        array('group' => 'CSV', 'name' => 'HO_CSV_CSR_STOCK', 'type' => 'str',  'value' => 'bestand.csv'),
        array('group' => 'CSV', 'name' => 'HO_CSV_ORDER', 'type' => 'str',  'value' => 'order.csv'),
        array('group' => 'FTP', 'name' => 'HO_FTP_SERVER', 'type' => 'str',  'value' => ''),
        array('group' => 'FTP', 'name' => 'HO_FTP_USER', 'type' => 'str',  'value' => ''),
        array('group' => 'FTP', 'name' => 'HO_FTP_PASS', 'type' => 'str',  'value' => ''),
		/* array('group' => 'main', 'name' => 'jCsvOption', 'type' => 'bool', 'value' => 'false'), */
    )
);