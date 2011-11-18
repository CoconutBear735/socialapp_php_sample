<?php if (!defined('BASEPATH')) exit('No direct script access class');

class Smarty_output {

	var $CI;
	
	function output()
	{
		// 定義されていない場合
		if (!defined('FLASH_OUTPUT') && !defined('BACKGROUND')) {
		
			Log::DebugOut("Smarty_output::output()");
			
			$this->CI =& get_instance();
			
			$carrier = $this->CI->config->item('CARRIER');
			
			$type = '';
			
			// キャリア別出力
			if ($carrier === DOCOMO) {
				$type = 'Content-Type: application/xhtml+xml; charset=Shift_JIS';
			}
			else if ($carrier === SOFTBANK) {
				$type = 'Content-Type: text/html; charset=UTF-8';
			}
			else if ($carrier === AU) {
				$type = 'Content-Type: text/html; charset=Shift_JIS';
			}
			
			$this->CI->output->set_header($type);
			
			$this->CI->output->_display($this->CI->output->get_output());
		}
	}
}

