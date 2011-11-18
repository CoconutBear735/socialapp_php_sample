<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game_corner extends E_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		// mobileフォルダへのパス
		$this->mobile_path = dirname(dirname(__FILE__)).'/';
		Log::DebugOut('mobile_path:'.$this->mobile_path);
		
		// workフォルダへのパス
		$this->current_work = $this->mobile_path.'work/';
		Log::DebugOut("current_work:".$this->current_work);
		
		// staticフォルダへのパス
		$this->static_path = $this->mobile_path.'static/';
		Log::DebugOut("static_path:".$this->static_path);
		
		$this->base_path = dirname(dirname(dirname(dirname(__FILE__))));
		Log::DebugOut("base_path:".$this->base_path);
		
		// 最大ステージ数
		$this->max_stage_id = $this->config->item('MAX_STAGE_ID');
		
		// ステージモデル
		$this->load->model('stage_model');
	}
	
	// 初期設定処理
	public function stage_select($in_page_num = 0)
	{
		Log::DebugOut("Game_corner::stage_select(in_page_num:".$in_page_num.")");
		
		$result = array();
		$stage_count = 0;
		
		$uid = $this->config->item('UID');
		$user_id = $this->user_model->get_user_id_by_uid($uid);
		
		if (0 < $user_id) {
		
			// クリアステージIDの中の最大ID
			$max_stage_id = $this->stage_model->get_max_stage_id($user_id);
			
			// プレイ可能ステージ数
			$play_stage_num = $max_stage_id + 1;
			
			// 上限チェック
			if ($this->max_stage_id < $play_stage_num) {
				$play_stage_num = $this->max_stage_id;
			}
			
			Log::DebugOut("play_stage_num:".$play_stage_num);
			
			$result['MAX_STAGE_ID'] = $play_stage_num;
			
			
			// ページ内の最大表示数
			$max_disp_num = 10;
			
			$result['stage_num'] = $play_stage_num;
			
			// 最大ページ数
			$result['max_page'] = floor(($play_stage_num - 1) / $max_disp_num);
			
			if ($result['max_page'] < $in_page_num) {
				$in_page_num = $result['max_page'];
			}
			
			// 現在の表示ページ番号
			$result['now_page'] = $in_page_num;
			
			$result['disp_max_page'] = $result['max_page'] + 1;
			$result['disp_now_page'] = $result['now_page'] + 1;
			
			if ($result['disp_max_page'] <= 0) {
				$result['disp_max_page'] = 1;
			}
			
			// 遷移用URL
			$result['next_page_url'] = $in_page_num + 1;
			$result['prev_page_url'] = $in_page_num - 1;
			
			// ループ開始インデックス
			$result['start_index'] = ($max_disp_num * $in_page_num);
			
			// ループ回数
			$loop_num = $result['start_index'] + $max_disp_num;
			
			if ($play_stage_num < $max_disp_num) {
				$loop_num = $play_stage_num;
			}
			// 最後のページの場合
			else if($result['now_page'] == $result['max_page']) {
				$loop_num = $play_stage_num;
			}
			
			$result['loop_num'] = $loop_num;
			
			// ページ内容ループ
			for ($i = $result['start_index']; $i < $loop_num; $i++) {
			
				$stage_id = $i + 1;
				
				$flash_url[$i]['URL'] = $this->add_zero($stage_id, 'mobile/game_stg', '.gif', FALSE);
				$flash_url[$i]['STAGE_ID'] = $stage_id;
			}
			
			Log::ObjectDataOut($flash_url);
			
			$result['FLASH_URL'] = $flash_url;
		}
		
		$this->my_smarty->assign('UID', $uid);
		$this->my_smarty->view('stage_select', $result);
		
		Log::DebugOut("Game_corner::stage_select() end");
	}
	
	public function game_start($stage_id = 0)
	{
		Log::DebugOut("Game_corner::game_start(stage_id:".$stage_id.")");
		
		$result = array();
		$stage_count = 0;
		
		$uid = $this->config->item('UID');
		$user_id = $this->user_model->get_user_id_by_uid($uid);
		
		if (0 < $user_id) {
		
			// ステージIDの不正チェック
			$stage_id = $this->_check_playable_id($user_id, $stage_id);
			Log::DebugOut("check_stage_id:".$stage_id);
			
			$result['STAGE_ID'] = $stage_id;
			$result['STAGE_LOGO'] = $this->add_zero($stage_id, 'mobile/gallery_', '_b.gif');
		}
		
		$this->my_smarty->assign('UID', $uid);
		$this->my_smarty->view('game_start', $result);
		
		Log::DebugOut("Game_corner::game_start() end");
	}
	
	public function game_play($stage_id = 0)
	{
		Log::DebugOut("Game_corner::game_play(stage_id:".$stage_id.")");
		
		if (($stage_id <= 0) || ($this->max_stage_id < $stage_id)) {
			return FALSE;
		}
		
		global $RO;
		
		$uid = $this->config->item('UID');
		$user_id = $this->user_model->get_user_id_by_uid($uid);
		
		if (0 < $user_id) {
		
			// ステージIDの不正チェック
			$stage_id = $this->_check_playable_id($user_id, $stage_id);
			Log::DebugOut("check_stage_id:".$stage_id);
			
			// ユーザディレクトリ名
			$dir_num = $user_id / 10000;
			$dir_num = floor($dir_num);
			
			// 基数ディレクトリのパス
			$cardinal_number_path = $this->current_work.$dir_num;
			Log::DebugOut("cardinal_number_path:".$cardinal_number_path);
			
			// ユーザディレクトリのパス
			$user_work = $cardinal_number_path.'/'.$user_id;
			Log::DebugOut("user_work:".$user_work);
			
			$this->_make_dir($cardinal_number_path);
			$this->_make_dir($user_work);
			
			$this->my_smarty->assign('PROXY', FALSE);
		
			// JSONファイル読み込み／書き込みモードで開く
			if ($stage_id < 10) {
				$file_name = $this->base_path.'/path/stage00'.$stage_id;
			}
			else {
				$file_name = $this->base_path.'/path/stage0'.$stage_id;
			}
			
			// JSONファイルの読み込み
			$json = json_decode(file_get_contents($file_name.'.json'), TRUE);
			
			// json配列情報の読み込み
			require_once($this->static_path.'stage_array.php');
			
			// 遷移先アドレス
			$flash_url = $this->my_smarty->helper->flash_url('game_corner/game_result/'.$stage_id);
			Log::DebugOut("flash_url:".$flash_url);
			
			$query_string = '/'.urlencode('?guid=ON&rnd='.$RO->SID);
			Log::DebugOut("query_string:".$query_string);
			
			// ファイル名取得
			$swf_name = "filename";
			
			$game_swf = $this->static_path.$swf_name.'.swf';
			$game_flm = $this->static_path.$swf_name.'.flm';
			Log::DebugOut("game_swf:".$game_swf);
			Log::DebugOut("game_flm:".$game_flm);
			
			$temp_swf = $user_work.'/'.$swf_name.'.swf';
			$temp_flm = $user_work.'/'.$swf_name.'.flm';
			Log::DebugOut("temp_swf:".$temp_swf);
			Log::DebugOut("temp_flm:".$temp_flm);
			
			// flmの読み込み
			require_once($game_flm);
			
			// swfのコピー
			copy($game_swf, $temp_swf);
			
			// 文字コードの変換
			$flm = mb_convert_encoding($flm, 'SJIS', 'EUC-JP');
			
			// コピーへの書き込み
			file_put_contents($temp_flm, $flm);
			
			// 合成用実行ファイル
			$flasm = $this->mobile_path."bin/flasm";
			
			// 合成の実行
			$cmd = "{$flasm} -a {$temp_flm}";
			shell_exec($cmd);
			
			if (file_exists($temp_swf)) {
				
				define('FLASH_OUTPUT', 1);
				
				$RO->FLASH_OBJ = file_get_contents($temp_swf);
				
				// SID更新
				$this->user_model->update_req_user_id($user_id, $RO->SID);
			}
			
			// 不要データの削除
			@unlink($temp_flm);
			@unlink($user_work.'/'.$swf_name.'.$wf');
			@unlink($temp_swf);
		}
		
		Log::DebugOut("Game_corner::game_play() end");
	}
	
	function game_result($stage_id = 0, $clear_flag = 0)
	{
		Log::DebugOut("Game_corner::game_result(stage_id:".$stage_id.", clear_flag:".$clear_flag.")");
		
		// ステージ数の不正チェック
		if ($stage_id <= 0) {
			return FALSE;
		}
		
		$result = array();
		
		global $RO;
		
		$uid = $this->config->item('UID');
		$user_id = $this->user_model->get_user_id_by_uid($uid);
		
		if ($clear_flag == GAME_OVER) {
		
		
		}
		// クリア時
		else if ($clear_flag == GAME_CLEAR) {
		
			$rnd = $this->config->item('rnd');
			
			if ($this->user_model->valid_req($uid, $rnd)) {
				
				// クリアデータチェック
				$stage_clear_id = $this->stage_model->get_stage_clear_id($user_id, $stage_id);
				
				// 未クリア
				if ($stage_clear_id == 0) {
				
					$stage_clear_id = $this->stage_model->insert_stage_clear($user_id, $stage_id);
					
					// ユーザデータに最新のクリアデータIDを保存
					$this->user_model->update_user_stage_clear_id($user_id, $stage_clear_id);
				}
				else {
					$this->stage_model->update_stage_clear($stage_clear_id);
				}
				
				// SID更新
				$this->user_model->update_req_user_id($user_id, $RO->SID);
			}
			// 再リクエスト等
			else {
			
				// クリアステージ数
				$stage_id_array = array();
				$stage_id_array = $this->stage_model->get_stage_clear($user_id, 0, SORT_UPDATE_DATE);
				
				$last_clear_id = 1;
				
				// 最終クリアステージIDの取り出し
				if (has_value($stage_id_array)) {
					$last_clear_id = $stage_id_array[0]['STAGE_ID'];
				}
				
				Log::DebugOut("last_clear_id:".$last_clear_id);
				
				// ステージクリアIDを最新クリアデータのIDで更新
				$stage_id = abs($last_clear_id);
			}
			
			// 表示CGの名前取得
			$stage_cg = $this->config->item('stage_cg');
			$file_name = $stage_cg[$stage_id];
			
			// ギャラリー表示準備
			$result['FILE_NAME'] = 'mobile/'.$file_name;
		}
		else {
		
			// ゲームオーバー遷移
			$clear_flag = GAME_OVER;
		}
		
		$result['CLEAR_FLAG'] = $clear_flag;
		
		// ステージ選択画面へ戻った時のページ数
		$result['page_num'] = floor($stage_id / 10);
		
		$this->my_smarty->assign('UID', $uid);
		$this->my_smarty->view('game_result', $result);
		
		Log::DebugOut("Game_corner::game_result() end");
	}
	
	private function _check_playable_id($user_id = 0, $stage_id = 0)
	{
		Log::DebugOut("check_playable_id(user_id:".$user_id.", stage_id:".$stage_id.")");
		
		// クリアステージIDの中の最大ID
		$max_stage_id = $this->stage_model->get_max_stage_id($user_id);
		
		// プレイ可能ステージ数
		$play_stage_num = $max_stage_id + 1;
		Log::DebugOut("play_stage_num:".$play_stage_num);
		
		// 送信IDがまだプレイ不可のステージ
		if ($play_stage_num < $stage_id) {
			$stage_id = $play_stage_num;
		}
		
		// 上限チェック
		if ($this->max_stage_id < $stage_id) {
			$stage_id = $this->max_stage_id;
		}
		
		return $stage_id;
	}
	
	private function _make_dir($path = FALSE)
	{
		Log::DebugOut("_make_dir(path:".$path.")");
		
		$result = FALSE;
		
		if ($path !== FALSE) {
		
			// ファイルの存在チェック
			if (!file_exists($path)) {
				$result = mkdir($path, 0755);
			}
			// 既に作成済み
			else {
				$result = TRUE;
			}
		}
		
		return $result;
	}

}

/* End of file game_corner.php */
/* Location: ./application/controllers/game_corner.php */
