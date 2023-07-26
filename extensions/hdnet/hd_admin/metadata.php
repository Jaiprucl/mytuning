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

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'           									=> 'hd_admin',
    'title'        									=> 'HDNET Administration',
    'description'  									=> 'HDNET Administrationstool',
    'thumbnail'    									=> 'hd.png',
    'version'      									=> '1.0.9',
    'author'       									=> 'Christopher Olhoeft',
    'url'          									=> 'https://www.hdnet.de/',
    'email'        									=> 'christopher.olhoeft@hdnet.de',
    'extend' => array(
		'hd_vexport'                                => hd_vexport::class,
		'hd_vimport'                                => hd_vimport::class,
		'hd_picture'                                => hd_picture::class,
        'hd_file'                                   => hd_file::class,
        /* 'hd_export'                                 => hd_export::class, */
        'hd_import'                                 => hd_import::class,
        'hd_logs'                                   => hd_logs::class,
    ),
    'templates' => array(
        'hd_file.tpl'  							    => 'hdnet/hd_admin/out/admin/tpl/hd_file.tpl',
        'hd_export.tpl' 						    => 'hdnet/hd_admin/out/admin/tpl/hd_export.tpl',
        'hd_import.tpl' 						    => 'hdnet/hd_admin/out/admin/tpl/hd_import.tpl',
        'hd_logs.tpl' 							    => 'hdnet/hd_admin/out/admin/tpl/hd_logs.tpl',
        'hd_vimport.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_vimport.tpl',
        'hd_picture.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_picture.tpl',
        'hd_vexport.tpl' 						    => 'hdnet/hd_admin/out/tpl/hd_vexport.tpl',
    ),
	
	'blocks' => array(
        array('template' => 'actions_main.tpl', 'block'=>'admin_actions_main_form', 'file'=>'out/blocks/actions_main_block.tpl'),
    ),
	
	'settings' => array(
        array('group' => 'PATH', 'name' => 'HD_IMPORT_CSR_ARTICLE_PATH', 'type' => 'str',  'value' => 'modules/hdnet/hd_admin/export/in/Artikel/'),
        array('group' => 'PATH', 'name' => 'HD_IMPORT_RIEGER_ARTICLE_PATH', 'type' => 'str',  'value' => 'modules/hdnet/hd_admin/export/in/Artikel/Rieger/'),
        array('group' => 'PATH', 'name' => 'HD_IMPORT_FK_ARTICLE_PATH', 'type' => 'str',  'value' => 'modules/hdnet/hd_admin/export/in/Artikel/FK/'),
        array('group' => 'PATH', 'name' => 'HD_IMPORT_CSR_STOCK_PATH', 'type' => 'str',  'value' => 'modules/hdnet/hd_admin/export/in/Bestand/'),
        array('group' => 'PATH', 'name' => 'HD_EXPORT_PATH', 'type' => 'str',  'value' => 'modules/hdnet/hd_admin/export/out/'),
        array('group' => 'CSV', 'name' => 'HD_CSV_CSR_ARTICLE', 'type' => 'str',  'value' => 'exp_produkte.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_RIEGER_ARTICLE', 'type' => 'str',  'value' => 'article.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_FK_ARTICLE', 'type' => 'str',  'value' => 'ALL_ACTIVE_FK_ARTICLE_WITH_STOCK_de.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_CSR_STOCK', 'type' => 'str',  'value' => 'bestand.csv'),
        array('group' => 'CSV', 'name' => 'HD_CSV_ORDER', 'type' => 'str',  'value' => 'order.csv'),
        array('group' => 'FTP', 'name' => 'HD_FTP_SERVER', 'type' => 'str',  'value' => ''),
        array('group' => 'FTP', 'name' => 'HD_FTP_USER', 'type' => 'str',  'value' => ''),
        array('group' => 'FTP', 'name' => 'HD_FTP_PASS', 'type' => 'str',  'value' => ''),
    )
);