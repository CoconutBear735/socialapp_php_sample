<?php

require_once('./log.php');

class Action {

	function execute($path, $vars = false)
	{
		Log::DebugOut("execute(path:".$path.", vars = array())");
		Log::OBjectDataOut($vars);
	
		define('AMFPHP', 1);
		
		global $value, $arrData;
		
		if ($vars && is_array($vars)){
			// Convert vars to POST data
			$_POST = $vars;
		}
		
		// query info
		$_SERVER['PATH_INFO'] = '/'.$path;
		$_SERVER['QUERY_STRING'] = '/'.$path;
		$_SERVER['REQUEST_URI'] = '/'.$path;
		
		Log::DebugOut("action start");
		
		require_once(INDEX_PAGE);
		
		Log::DebugOut("action end");
		
		return $arrData;
	}
}
