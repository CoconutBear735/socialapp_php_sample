<?php

define('INDEX_PAGE', '/socialapp_php_sample/www/index.php');

$_AMFPHP_DIR =  '/socialapp_php_sample/amfphp';  // AMFPHPディレクトリへのパス
include $_AMFPHP_DIR.'/core/amf/app/Gateway.php';

$gateway = new Gateway();

$gateway->disableDebug();
$gateway->setClassPath($_AMFPHP_DIR . '/services/'); // サービスクラスの場所
$gateway->enableGzipCompression(25 * 1024);
$gateway->service();
