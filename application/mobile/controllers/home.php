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
		
		global $RO;
		
		$result = array();
		
		// --- UIDの取得
		$uid = $this->config->item('UID');
		
		$this->my_smarty->assign('UID', $uid);
		$this->my_smarty->view('home', $result);
		
		Log::DebugOut("Home::index() end");
	}
	
	// 非対応端末ページ生成
	function unsupport_page()
	{
		Log::DebugOut("Home::unsupport_page()");
		
		global $RO;
		
		$result = array();
		
		$this->my_smarty->view('unsupported', $result);
		
		Log::DebugOut("Home::unsupport_page() end");
	}
	
	// 対応機種ページ生成
	function device_info_page()
	{
		Log::DebugOut("Home::device_info_page()");
		
		global $RO;
		
		$result = array();
		
		$this->my_smarty->view('device_info', $result);
		
		Log::DebugOut("Home::device_info_page() end");
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */