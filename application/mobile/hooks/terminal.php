<?php if (!defined('BASEPATH')) exit('No direct script access class');

class Terminal {

	var $CI;
	
	function Terminal()
	{
		// ルートオブジェクトの取得
		$this->CI =& get_instance();
	}
	
	function oauth_check()
	{
		Log::DebugOut("hook::oauth_check()");
		
		// プラットフォームデータ
		$container = $this->CI->config->item('container_info');
		$sandbox = $container['sandbox'];
		
		$consumer_key = $container['CONSUMER_KEY'];
		$consumer_secret = $container['CONSUMER_SECRET'];
		
		// 認証用ライブラリのロード
		$params = array('consumer_key' => $consumer_key, 'consumer_secret' => $consumer_secret);
		$this->CI->load->library('request_to_partner_server', $params);
		
		// 認証チェック
		$result = $this->CI->request_to_partner_server->check_server_signature();
		
		// 失敗
		if ((!$result) && (!$sandbox)) {
			$this->CI->output->set_status_header('401');
			exit();
		}
		
		// アプリID
		$this->CI->config->set_item('APP_ID', $this->CI->input->get('opensocial_app_id'));
		$this->CI->config->set_item('UID', $this->CI->input->get('opensocial_owner_id'));
		
		// デバイスタイプ
		$this->CI->config->set_item('DEVICE', 1);
	}
}
