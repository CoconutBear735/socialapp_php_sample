<?php

	// ���M���O�N���X
	class Log {
	
		// �g����
		// �P�D�C���N���[�h���܂��B���̐ݒ�p�X�͎��sphp�Ɠ����t�H���_�ɂ���ꍇ
		//require_once("./log.php");
		// �Q�D�o�͂�����������������ɓn���i���s�͂����Ăɂ���܂��j
		//Log::DebugOut("static test");
		
		static $file_name = "log.txt";
		
		function Log() {}
		
		// �����R�[�h�`�F�b�N�֐�
		function CheckCode($in_str = NULL, $in_com = NULL)
		{
			// �����R�[�h���o
			$code_type = mb_detect_encoding($in_str, "auto");
			
			// ���o�s��
			if ($code_type == FALSE) {
				$code_type = "unknows_code";
			}
			
			Log::DebugOut("str:".$in_str.", code_type:".$code_type);
		}
		
		static function DebugOut($in_str = "", $type = -1)
		{
			//if ($type != -1) {
			
				// ���s�}��
				$in_str.="\n";
				
				// �p�X�̍쐬
				$log_path = (dirname(__FILE__)."/log/log.txt");
				
				error_log($in_str, 3, $log_path);
			//}
		}
		
		// �w�肳�ꂽ�I�u�W�F�N�g�̃N���X����Ԃ��A�N���X�ł͂Ȃ��ꍇ��false��Ԃ�
		static function ObjectDataOut($in_obj = -1, $type = -1)
		{
			//if ($type != -1) {
			
				Log::DebugOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", 0);
				
				$ret = get_class($in_obj);
				
				if ($ret == FALSE) {
				
					if (is_array($in_obj)) {
					
						$count = 0;
						
						foreach($in_obj as $key => $value) {
						
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
					
					// ��������HTML�o�͂����
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
			//}
		}
	}