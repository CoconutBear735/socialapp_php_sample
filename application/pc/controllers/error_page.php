<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error_page extends ELE_Controller {

	public function index()
	{
		Log::DebugOut("Error_page::index()");
		
		// 送信データの設定
		echo 'Unauthorized access';
		
		exit();
	}
}

/* End of file error_page.php */
/* Location: ./application/controllers/error_page.php */