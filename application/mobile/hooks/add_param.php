<?php if (!defined('BASEPATH')) exit('No direct script access class');

class Add_param {

	var $CI;
	
	function __construct()
	{
		// ルートオブジェクトの取得
		$this->CI =& get_instance();
	}
	
	function param_check()
	{
		Log::DebugOut("hook::param_check()");
		
		global $RO;
		
		//コールバック関連はチェックしない
		$c_name = $this->CI->uri->segment(1, 'home');
		Log::DebugOut("c_name:".$c_name);
		
		if ($c_name === 'callback') {
			// バックグラウンド設定
			define('BACKGROUND', 1);
		}
		
		// 必要パラメータ確認
		$guid = $this->CI->input->request('guid');
		$rnd  = $this->CI->input->request('rnd');
		
		if (!$guid || !$rnd) {
			
			$query_string = $this->CI->input->server('QUERY_STRING');
			
			$url = site_url()."?". $query_string;
			
			if (!$guid) {
				$url .= "&guid=ON";
			}
			
			if (!$rnd) {
				$url .= '&rnd='.$RO->SID;
			}
			
			Log::DebugOut('url:'.$url);
			
			redirect($url, 'location');
		}
		
		// エージェント情報等の設定
		if (!$this->set_agent_param()) {
			
			// 未対応ページ表示ではない場合のみリダイレクト
			$method = $this->CI->uri->segment(2, 'index');
			
			if ($method !== 'unsupport_page') {
			
				// 対応外端末の時の処理
				$url = site_url().'/home/unsupport_page/?guid=ON&rnd='.$RO->SID;
				
				redirect($url, 'location');
			}
		}
		
		// セッションID
		$this->CI->config->set_item('rnd', $rnd);
	}
	
	function set_agent_param()
	{
		Log::DebugOut("set_access_param()");
		
		// ユーザーアクセス情報
		$agent = $this->CI->input->server('HTTP_USER_AGENT');
		Log::DebugOut("agent:".$agent);
		
		$this->CI->config->set_item('AGENT', $agent);
		
		$model = '';
		$carrier= '';
		
		if (eregi("DoCoMo/2.0", $agent)) {
		
			$carrier = DOCOMO;
			
			preg_match("/DoCoMo\/2\.0 (.+?)\(c.+/", $agent, $matches);
			
			$model = $matches[1];
		}
		else if (eregi("DoCoMo/1.0", $agent)) {
		
			$carrier = DOCOMO;
			
			preg_match("/DoCoMo\/1\.0\/(.+?)\/.+/", $agent, $matches);
			
			if ($matches[1] == "") {
				preg_match("/DoCoMo\/1\.0\/(.+?)$/", $agent, $matches);
			}
			
			$model = $matches[1];
		}
		else if (eregi("J-PHONE", $agent)) {
			$carrier = SOFTBANK;
			$model = $this->CI->input->server('HTTP_X_JPHONE_MSNAME');
		}
		else if (eregi("MOT-[CV]980", $agent)) {
			$carrier = SOFTBANK;
			$model = $this->CI->input->server('HTTP_X_JPHONE_MSNAME');
		}
		else if (eregi("Vodafone", $agent)) {
			$carrier = SOFTBANK;
			$model = $this->CI->input->server('HTTP_X_JPHONE_MSNAME');
		}
		else if (eregi("SoftBank", $agent)) {
			$carrier = SOFTBANK;
			$model = $this->CI->input->server('HTTP_X_JPHONE_MSNAME');
		}
		else if (eregi("KDDI", $agent)) {
		
			$carrier = AU;
			
			preg_match("/KDDI\-(.+?) UP\.Browser\/.+/", $agent, $matches);//機種名取得
			
			$model = $matches[1];
		}
		else if (eregi("UP.Browser", $agent)) {
		
			$carrier = AU;
			
			preg_match("/UP\.Browser\/(.+?)-(.+?) UP\.Link.+/", $agent, $matches);//機種名取得
				
			$model = $matches[2];
		}
		// それ以外
		else {
		
			if (eregi("WILLCOM", $agent)) {
				$this->CI->config->set_item('CARRIER', WILLCOM);
			}
			
			return FALSE;
		}
		
		Log::DebugOut("carrier:".$carrier);
		Log::DebugOut("model:".$model);
		
		$this->CI->config->set_item('CARRIER', $carrier);
		$this->CI->config->set_item('MODEL', $model);
		
		// デバイスチェック
		if (!$this->check_device($model)) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	function check_device($user_device = FALSE)
	{
		Log::DebugOut("check_device(user_device:".$user_device.")");
		
		if ($user_device !== FALSE) {
		
			// 端末データロード
			$this->CI->config->load('devices');
			
			$devices = $this->CI->config->item('devices');
			
			// check device type
			foreach ($devices as $dev) {
			
				if (strpos($user_device, $dev) !== FALSE) {
					return TRUE;
				}
			}
		}
		
		return FALSE;
	}
}
