<?php
/**
 *    This file is part of Christopher Olhoeft
 *
 * @link      https://www.hosysteme.de/
 * @author    Christopher Olhoeft
 */

/**
 * Metadata version
 */


use HDNET\hdadmin\admin\hd_file;
use HDNET\hdadmin\admin\hd_import;
use HDNET\hdadmin\admin\hd_logs;
use HDNET\hdadmin\application\views\hd_picture;
use HDNET\hdadmin\application\views\hd_vexport;
use HDNET\hdadmin\application\views\hd_vimport;
use HDNET\hdadmin\Controller\articleController;
use HDNET\hdadmin\Controller\imageController;
use HDNET\hdadmin\Controller\importController;
use HDNET\hdadmin\Controller\stockController;

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'           									=> 'hd_admin',
    'title'        									=> 'HDNET Administration',
    'description'  									=> 'HDNET Administrationstool',
    'thumbnail'    									=> 'hd.png',
    'version'      									=> '1.0.9',
    'author'       									=> 'Christopher Olhoeft',
    'url'          									=> 'https://www.hdnet.de/',
    'email'        									=> 'christopher.olhoeft@hdnet.de',
    'controllers'  => [
        'importController'                          => importController::class,
        'articleController'                         => articleController::class,
        'imageController'                           => imageController::class,
        'stockController'                           => stockController::class,
    ],
    'extend' => [
		'hd_vexport'                                => hd_vexport::class,
		'hd_vimport'                                => hd_vimport::class,
		'hd_picture'                                => hd_picture::class,
        'hd_file'                                   => hd_file::class,
        'hd_import'                                 => hd_import::class,
        'hd_logs'                                   => hd_logs::class,
    ],
    'templates' => [
        'hd_file.tpl'  							    => 'hdnet/hd_admin/out/admin/tpl/hd_file.tpl',
        'hd_export.tpl' 						    => 'hdnet/hd_admin/out/admin/tpl/hd_export.tpl',
        'hd_import.tpl' 						    => 'hdnet/hd_admin/out/admin/tpl/hd_import.tpl',
        'hd_logs.tpl' 							    => 'hdnet/hd_admin/out/admin/tpl/hd_logs.tpl',
        'hd_vimport.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_vimport.tpl',
        'hd_picture.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_picture.tpl',
        'hd_vexport.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_vexport.tpl',
    ],
	
	'blocks' => [
        ['template' => 'actions_main.tpl', 'block'=>'admin_actions_main_form', 'file'=>'out/blocks/actions_main_block.tpl'],
    ],
	
	'settings' => [
        array('group' => 'PATH', 'name' => 'HD_IMPORT_CSR_ARTICLE_PATH', 'type' => 'str',  'value' => 'export/in/Artikel/'),
        array('group' => 'PATH', 'name' => 'HD_IMPORT_CSR_STOCK_PATH', 'type' => 'str',  'value' => 'export/in/Bestand/'),
        array('group' => 'PATH', 'name' => 'HD_EXPORT_PATH', 'type' => 'str',  'value' => 'export/out/'),
        array('group' => 'CSV', 'name' => 'HD_CSV_CSR_ARTICLE', 'type' => 'str',  'value' => 'exp_produkte.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_CSR_STOCK', 'type' => 'str',  'value' => 'bestand.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_ORDER', 'type' => 'str',  'value' => 'order.csv'),
    ],
];