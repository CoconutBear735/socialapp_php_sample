<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends E_Controller {
	
	function Home()
	{
		parent::__construct();
	}
	
	// 初期設定処理
	public function index()
	{
		Log::DebugOut("Home::index()");
		
		$result = array();
		
		// UIDの取得
		$uid = $this->config->item('UID');
		
		// 送信データの設定
		$this->set_response_array($result);
		
		Log::DebugOut("Home::index() end");
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */