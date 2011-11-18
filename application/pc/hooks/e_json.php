<?php if (!defined('BASEPATH')) exit('No direct script access class');

class E_Json {

	var $CI;
	
	function safe_getEncoding($str, $default='auto')
	{
		foreach (array('EUC-JP', 'SJIS', 'UTF-8') as $charset) {
		
			if ($str == mb_convert_encoding($str, $charset, $charset)) {
				return $charset;
			}
		}
	
		return $default;
	}
	
	function output()
	{
		// 定義されていない場合
		if (!defined('AMFPHP') && !defined('BACKGROUND')) {
			
			global $RO;
			Log::ObjectDataOut($RO->RESULT_OBJ);
			
			$this->CI =& get_instance();
			
			// コンテンツタイプをjsonに指定
			$this->CI->output->set_content_type('application/json');
			//$this->CI->output->set_header('Content-Type: text/javascript; charset=utf-8');
			
			$this->CI->output->_display(json_encode($RO->RESULT_OBJ));
		}
	}
}

