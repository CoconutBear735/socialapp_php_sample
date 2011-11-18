<?php if (!defined('AMFPHP')) exit('No direct script access allowed');

class Img extends E_Controller {

	function Img()
	{
		parent::__construct();
	}
	
	function get_image_data()
	{
		global $value, $arrData;
		
		$err = "";
		$length = 0;
		
		// pcへのパス
		$pc_path = dirname(dirname(__FILE__)).'/';
		Log::DebugOut('pc_path:'.$pc_path);
		
		// 取得ファイル名
		$file_name = 'filename';
		
		// 取得ファイルパス
		$file_path = '/path/'.$file_name;
		
		$arrData = FALSE;
		
		try {
		
			$handle = fopen($file_path, "r");
			
			if ($handle === FALSE) {
				Log::DebugOut("file open failed");
			}
			else {
			
				$length = filesize($file_path);
				Log::DebugOut("length:".$length);
				
				$arrData = new ByteArray(fread($handle, $length));
				
				fclose($handle);
			}
		}
		catch (Exception $ex) {
			Log::DebugOut("exception:".$ex->getMessage());
		}
		
		return $arrData;
	}
}
