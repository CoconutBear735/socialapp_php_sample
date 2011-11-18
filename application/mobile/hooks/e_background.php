<?php if (!defined('BASEPATH')) exit('No direct script access class');

class E_Background {

	var $CI;
	
	function output()
	{
		// 定義されている場合
		if(defined('BACKGROUND')) {
			
			global $RO;
			Log::ObjectDataOut($RO->RESULT_OBJ);
			
			$this->CI =& get_instance();
			
			$this->CI->output->set_content_type('text/plain');
			$this->CI->output->_display($RO->RESPONSE);
		}
	}
}

