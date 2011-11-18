<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class E_Controller extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	// レスポンス値の設定関数
	function set_response($data)
	{
		Log::DebugOut("set_response(data:".$data.")");
		
		if (!has_value($data)) {
			return FALSE;
		}
		
		global $RO;
		
		$RO->RESPONSE = $data;
		
		return TRUE;
	}
	
	// 渡された配列内のキーを元に値を全てセットする＿現状１次元
	function set_response_array($data_array = array())
	{
		Log::DebugOut("set_response_array(data_array)");
		Log::ObjectDataOut($data_array);
		
		if ((!has_value($data_array)) || (!is_array($data_array))) {
			return FALSE;
		}
		
		global $RO;
		Log::ObjectDataOut($RO->RESULT_OBJ);
		
		foreach ($data_array as $key => $val) {
			$RO->RESULT_OBJ[$key] = $val;
		}
		
		return TRUE;
	}
	
	function add_zero($in_num = -1, $address = '', $type = '', $flag = TRUE)
	{
		Log::DebugOut("add_zero(in_num:".$in_num.", address:".$address.", type:".$type.", flag:".$flag.")");
		
		if ($in_num <= -1) {
			return FALSE;
		}
		
		// ０詰め
		$num = abs($in_num);
		
		if ($flag === TRUE) {
			$num--;
		}
		
		$ret = '';
		
		// 10以下か
		if ($num < 10) {
			$ret = $address.'0'.$num.$type;
		}
		else {
			$ret = $address.$num.$type;
		}
		
		return $ret;
	}
}

// END E_Input class

/* End of file E_Controller.php */
/* Location: ./application/core/E_Controller.php */
