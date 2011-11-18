<?php

	// ロギングクラス
	class Log {
	
		// 使い方
		// １．インクルードします。下の設定パスは実行phpと同じフォルダにある場合
		//require_once("./log.php");
		// ２．出力したい文字列を引数に渡す（改行はかってにされます）
		//Log::DebugOut("static test");
		
		//[追記]インクルードパスの記述例
		// インクルードする側のファイルパスはdirname(__FILE__)で取得できるので
		// １つ上のフォルダにlog.phpがある場合は以下のようにパス指定で大丈夫なはず……
		// require_once(dirname(__FILE__).'/../log.php');
		
		// Log::$file_name = "ファイル名"; 変えられます。
		static $file_name = "log.txt";
		
		// コンストラクタ
		function Log() {}
		
		// 文字コードチェック関数
		function CheckCode($in_str = NULL, $in_com = NULL)
		{
			// 文字コード検出
			$code_type = mb_detect_encoding($in_str, "auto");
			
			// 検出不可
			if ($code_type == FALSE) {
				$code_type = "unknows_code";
			}
			
			Log::DebugOut("str:".$in_str.", code_type:".$code_type);
		}
		
		// リリース時は切り替えよう
		static function DebugOut($in_str = "", $type = -1)
		{
			if ($type != -1) {
			
				// 改行挿入
				$in_str.="\n";
				
				// パスの作成
				//$log_path = (dirname(__FILE__)."/log/log.txt");
				$log_path = (dirname(__FILE__)."/log/".Log::$file_name);
				
				error_log($in_str, 3, $log_path);
			}
		}
		
		// 指定されたオブジェクトのクラス名を返す、クラスではない場合はfalseを返す
		static function ObjectDataOut($in_obj = -1, $type = -1)
		{
			// リリースかデバッグか？
			if ($type != -1) {
			
				Log::DebugOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0);
				
				$ret = get_class($in_obj);
				
				if ($ret == FALSE) {
				
					if (is_array($in_obj)) {
					
						$count = 0;
						
						foreach($in_obj as $key => $value) {
						
							//Log::DebugOut("THIS OBJ IS ARRAY");
							//Log::DebugOut("NAME:".$key);
							//Log::DebugOut("VAL:".$value);
							
							if (is_array($value)) {
							
								foreach($value as $key2 => $value2) {
									Log::DebugOut("NAME2_1:".$key, 0);
									Log::DebugOut("VAL2_1:".$value, 0);
									Log::DebugOut("ANAME2:".$key2, 0);
									Log::DebugOut("AVAL2:".$value2, 0);
								}
							}
							else {
								Log::DebugOut("NAME:".$key, 0);
								Log::DebugOut("VAL:".$value, 0);
							}
							
							if ($count == 0) {
								$count++;
							}
						}
						
						if ($count == 0) {
							Log::DebugOut("THIS OBJ IS NO ARRAY:".$in_obj, 0);
						}
					}
					else {
						//Log::DebugOut("THIS OBJ NO ARRAY");
						Log::DebugOut("THIS OBJ NO CLASS:".$in_obj, 0);
					}
				}
				else {
				
					Log::DebugOut("THIS CLASS NAME:".$ret, 0);
					
					// こっちはHTML出力される
					//Log::DebugOut(var_dump(get_object_vars($in_obj)));
					
					$array_ret = get_object_vars($in_obj);
					
					foreach($array_ret as $key => $value) {
					
						if (is_array($value)) {
						
							foreach($value as $key => $value) {
								
								Log::DebugOut("ANAME:".$key, 0);
								Log::DebugOut("AVAL:".$value, 0);
							}
						}
						else {
							Log::DebugOut("NAME:".$key, 0);
							Log::DebugOut("VAL:".$value, 0);
						}
					}
				}
			
				Log::DebugOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0);
			}
		}
		
		// リリース用
		//function DebugOut($in_str = "") {}
		//function function ClassNameOut($in_obj) {}
	}