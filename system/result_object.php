<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Result_object {

	var $RESULT_OBJ;
	var $SID;
	
	var $FLASH_OBJ;
	
	var $RESPONSE;
	var $OUTPUT_TYPE;
	
	function Result_object()
	{
		Log::DebugOut("__constract:Result_object");
		
		$this->RESULT_OBJ = array();
		
		$this->SID = md5(mt_rand());
	}
}

$RO = new Result_object;

/* End of file result_object.php */
/* Location: ./system/result_object.php */