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

use CO\mailattachment\Core\COEmail;
use OxidEsales\Eshop\Core\Email;

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'           									=> 'mailattachment',
    'title'        									=> '<strong style="color:#cc0d0d;">CO</strong> MailAttachment',
    'description'  									=> 'Mail Attachment',
    'thumbnail'    									=> 'hd.png',
    'version'      									=> '1.0.1',
    'author'       									=> 'Christopher Olhoeft',
    'url'          									=> 'https://www.hdnet.de/',
    'email'        									=> 'christopher.olhoeft@hdnet.de',
    'controllers'  => [],
    'extend' => array(
        Email::class                                 => COEmail::class,
    ),
    'blocks' => array(
        array('template' => 'email/html/order_cust.tpl', 'block'=>'email_html_order_cust_orderemailend', 'file'=>'out/blocks/email_html_order_cust_orderemailend.tpl'),
    ),
    'settings' => array(
        array('group' => 'PATH', 'name' => 'CO_AGB_FILE', 'type' => 'str',  'value' => 'out/downloads/AGB.pdf'),
        array('group' => 'PATH', 'name' => 'CO_WITHDRAWAL_FILE', 'type' => 'str',  'value' => 'out/downloads/Widerrufsbelehrung.pdf'),
    )
);