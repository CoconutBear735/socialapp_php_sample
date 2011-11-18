<?php if (!defined('BASEPATH')) exit('No direct script access class');

class Amfphp {

	var $ci;
	
	function output()
	{
		if(!defined('AMFPHP')) {
		
			Log::DebugOut("amf");
			
			$this->ci =& get_instance();
			
			Log::DebugOut($this->ci->output->get_output());
			
			$this->ci->output->_display($this->ci->output->get_output());
		}
	}
}

