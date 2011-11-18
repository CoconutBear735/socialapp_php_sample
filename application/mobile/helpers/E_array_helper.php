<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("load E_array_helper");

if ( ! function_exists('has_value'))
{
	function has_value($data)
	{
		if (isset($data) AND !empty($data)) return TRUE;
		return FALSE;
	}
}

if ( ! function_exists('get_array_array'))
{
	// 指定した要素の配列だけを抜き出す
	function get_array_array($src_array = array(), $index = '')
	{
		Log::DebugOut("get_array_array()");
		
		// まずは配列の存在確認
		if (!has_value($src_array)) {
			return FALSE;
		}
		
		// （最初の要素の中に指定）要素が存在するか？
		if (!(array_key_exists($index, $src_array[0]))) {
	    	// 存在しない
			return FALSE;
		}
		
		$new_array = array();
		
		// 取り出し
		foreach ($src_array as $key => $val) {
			$new_array[$key] = $val[$index];
		}
		
		return $new_array;
	}
}

/* End of file E_array_helper.php */
/* Location: ./application/helpers/E_array_helper.php */