<?php
/**
 *    This file is part of Christopher Olhoeft
 *
 * @author    Christopher Olhoeft
 */
/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           									    => 'co_email',
    'title'        									    => 'CO Email Attachment',
    'description'  									    => 'Email Anhang konfigurieren',
    'thumbnail'    									    => 'ho.png',
    'version'      									    => '1.0.0',
    'author'       									    => 'Christopher Olhoeft',
    'email'        									    => 'c.olhoeft@gmail.com',
    'extend' => array(
		\OxidEsales\Eshop\Core\Email::class             => 'co/MailAttachment/Core/Email',
    ),
	'blocks' => array(
        array('template' => 'email/html/order_cust.tpl', 'block'=>'email_html_order_cust_orderemailend', 'file'=>'out/blocks/email_html_order_cust_orderemailend.tpl'),
    ),
	'settings' => array(
        array('group' => 'PATH', 'name' => 'CO_AGB_FILE', 'type' => 'str',  'value' => 'out/downloads/AGB.pdf'),
        array('group' => 'PATH', 'name' => 'CO_WITHDRAWAL_FILE', 'type' => 'str',  'value' => 'out/downloads/Widerrufsbelehrung.pdf'),
    )
);