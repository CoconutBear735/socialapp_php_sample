<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['container_info'] = array(

	'APP_ID'			=> '',
	
	// 認証用データ
	'oauth_consumer_key'		=> "",
	'opensocial_app_url'		=> "http://host.com/socialapp_php_sample/gadget.xml",
	'oauth_signature_method'	=> "RSA-SHA1",
	'sandbox'			=> FALSE,
	
	// アクセス用データ
	'CONSUMER_KEY'			=> '',
	'CONSUMER_SECRET'		=> '',
	
	// APISERVER ADDRESS
	'API_URL'			=> '',
	'RESTFUL_URL'			=> '',
	'PLATFORM_URL'			=> '',
);

/* End of file platform.php */
/* Location: ./application/config/platform.php */