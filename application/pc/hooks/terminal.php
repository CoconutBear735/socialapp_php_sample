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
		Log::DebugOut("oauth_check()");
		
		if(!defined('AMFPHP')) {
			
			// 参照先確認
			$refferer = $this->CI->input->server('HTTP_REFERER');
			
			// プラットフォームデータ
			$container = $this->CI->config->item('container_info');
			$sandbox = $container['sandbox'];
			
			if ($refferer === FALSE) {
			
				$c_name = $this->CI->uri->segment(1, 'home');
				Log::DebugOut("c_name:".$c_name);
				
				if ($c_name !== 'callback') {
				
					// 認証用オブジェクト
					$result = $this->CI->signed_request_validator->validate_request($container['opensocial_app_url']);
					
					// 失敗
					if ((!$result) && (!$sandbox)) {
					
						$this->CI->output->set_status_header('401');
						//$this->CI->output->_display(json_encode(array('ERROR' => '401')));
						
						exit();
					}
				}
				// ペイメントコールバック時のみ2OAuthでチェック
				else {
					
					$consumer_key = $container['CONSUMER_KEY'];
					$consumer_secret = $container['CONSUMER_SECRET'];
					
					// 認証用ライブラリのロード
					$params = array('consumer_key' => $consumer_key, 'consumer_secret' => $consumer_secret);
					$this->CI->load->library('request_to_partner_server', $params);
					
					// 認証チェック
					$result = $this->CI->request_to_partner_server->check_server_signature();
					
					// 失敗
					if ((!$result) && (!$sandbox)) {
						exit();
					}
					
					// バックグラウンド設定
					define('BACKGROUND', 1);
				}
				
				// アプリID
				$this->CI->config->set_item('APP_ID', $this->CI->input->request('opensocial_app_id'));
				
				// オーナーID及びビューアーID
				$uid = $this->CI->input->request('opensocial_owner_id');
				$vid = $this->CI->input->request('opensocial_viewer_id');
				
				Log::DebugOut("UID:".$uid);
				Log::DebugOut("VID:".$vid);
				
				if ($vid !== FALSE) {
				
					// 持ち主と訪問主が同じかどうか？
					if ($uid != $vid) {
						// ビューワーで常に上書き
						$uid = $vid;
					}
				}
				else {
					$vid = $uid;
				}
			}
			else {
			
				// ユーザーアクセス情報
				$agent = $this->CI->input->server('HTTP_USER_AGENT');
				Log::DebugOut("agent:".$agent);
				
				if (eregi("Android 2.2", $agent) || eregi("Android 2.3", $agent)) {
				
					Log::DebugOut('2.2 or 2.3');
					
					$auth_array = array();
					
					$query = substr($refferer, (strpos($refferer, '?') + 1));
					$query = explode('&', $query);
					
					//Log::ObjectDataOut($query);
					
					foreach ($query as $key => $val) {
						$temp = explode('=', $val);
						$auth_array[$temp[0]] = $temp[1];
					}
					
					$_GET = $auth_array;
					$_REQUEST = $auth_array;
					
					$this->CI->load->library('touch_request_validator', $auth_array);
					
					$uid = $auth_array['opensocial_owner_id'];
					$vid = $auth_array['opensocial_viewer_id'];
					
					// 認証用オブジェクト
					$result = $this->CI->touch_request_validator->validate_request($container['opensocial_app_url']);
				}
				else {
				
					$this->CI->output->set_content_type('application/json');
					$this->CI->output->_display(json_encode(array('UNSUPPORT' => 1)));
					
					exit();
				}
			}
			
			$this->CI->config->set_item('UID', $uid);
			$this->CI->config->set_item('VID', $vid);
			
			// デバイスタイプ
			$this->CI->config->set_item('DEVICE', 0);
		}
	}
}
