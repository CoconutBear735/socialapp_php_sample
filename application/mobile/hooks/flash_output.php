<?php if (!defined('BASEPATH')) exit('No direct script access class');

class Flash_output {

	var $CI;
	
	function output()
	{
		if(defined('FLASH_OUTPUT')) {
		
			Log::DebugOut("Flash_output::output()");
			
			global $RO;
			
			$this->CI =& get_instance();
			
			$this->CI->output->set_header('Cache-Control: no-cache, must-revalidate');
			$this->CI->output->set_header('Content-Type: application/x-shockwave-flash');
			
			$this->CI->output->_display($RO->FLASH_OBJ);
		}
	}
}

