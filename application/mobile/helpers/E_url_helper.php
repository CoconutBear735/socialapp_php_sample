<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("load E_url_helper");

if (!function_exists('resource_url'))
{
	function resource_url($url = '')
	{
		Log::DebugOut("resource_url(url:".$url.")");
		
		$result = $url;
		
		if (strpos($url, "http") !== 0) {
			$result = site_url().$url;
		}
		
		Log::DebugOut("resource_url:".$result);
		
		return $result;
	}
}

// Platform URL
if ( ! function_exists('platform_url'))
{
	function platform_url()
	{
		$CI =& get_instance();
		
		$container_info = $CI->config->item('container_info');
		
		return $container_info['PLATFORM_URL'];
	}
}

// APP_ID
if ( ! function_exists('app_id'))
{
	function app_id()
	{
		$CI =& get_instance();
		
		return $CI->config->item('APP_ID');
	}
}

/* End of file E_url_helper.php */
/* Location: ./application/helpers/E_url_helper.php */