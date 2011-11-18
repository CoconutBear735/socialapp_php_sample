<?php if (!defined('BASEPATH')) exit('No direct script access class');

class ELE_Json {

	var $CI;
	
	function output()
	{
		// 定義されていない場合
		if(!defined('AMFPHP')) {
		
			Log::DebugOut("E_Json:output()");
			
			global $RO;
			Log::ObjectDataOut($RO->RESULT_OBJ);
			
			$this->CI =& get_instance();
			
			// コンテンツタイプをjsonに指定
			$this->CI->output->set_content_type('application/json');
			$this->CI->output->_display(json_encode($RO->RESULT_OBJ));
		}
	}
}

