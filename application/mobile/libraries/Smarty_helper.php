<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("Smarty_helper load");

class Smarty_helper {

	function resource_url($url = '')
	{
		Log::DebugOut("resource_url(url:".$url.")");
		
		$result = $url;
		
		if (strpos($url, "http") !== 0) {
			$result = base_url().'resource/'.$url;
		}
		
		Log::DebugOut("resource_url:".$result);
		
		return $result;
	}
	
	function site_url($path = FALSE, $params = array(), $proxy = TRUE)
	{
		Log::DebugOut("site_url(path:".$path.", params, proxy:".$proxy.")");
		Log::DebugOut("params:".$params);
		
		if ($path === FALSE) {
			return FALSE;
		}
		
		if (is_array($params)) {
			$query = implode('/', $params);
		}
		else {
			$query = $params;
		}
		
		$url = base_url().index_page(). '/'.$path.'/';
		
		Log::DebugOut("url:".$url);
		
		if (!empty($query)) {
			$url .= $query.'/';
		}
		
		global $RO;
		
		$url.= '?guid=ON&rnd='.$RO->SID;
		
		return $this->full_url($url, $proxy);
	}
	
	function full_url($url = FALSE, $proxy = FALSE)
	{
		Log::DebugOut("full_url(url:".$url.", proxy:".$proxy.")");
		
		if ($url === FALSE) {
			return FALSE;
		}
		
		$result = '';
		
		if ($proxy === TRUE) {
			$result = '?guid=ON&signed=1&url='.urlencode($url);
		}
		else {
			$result = $url.'&signed=1';
		}
		
		Log::DebugOut("full url:".$result);
		
		return $result;
	}
	
	function flash_url($path = FALSE)
	{
		Log::DebugOut("flash_url(path:".$path.")");
		
		if ($path === FALSE) {
			return FALSE;
		}
		
		$platform_url = platform_url().app_id().'/';
		
		$url = base_url().index_page(). '/'.$path.'/';
		
		return $platform_url.'?guid=ON&signed=1&url='.urlencode($url);
	}
}
