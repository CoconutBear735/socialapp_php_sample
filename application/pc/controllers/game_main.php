<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game_main extends E_Controller {
	
	function Game_main()
	{
		parent::__construct();
		
		// 必要モデル
		$this->load->model('record_model');
		$this->load->model('results_model');
	}
	
	// 初期設定処理
	public function _index()
	{
		Log::DebugOut("Game_main::index()");
		
		$result = array();
		$user_id = 0;
		
		// UIDの取得
		$uid = $this->config->item('UID');
		
		$user_info = $this->user_model->is_user_id($uid);
		Log::ObjectDataOut($user_info);
		
		$result['USER_ID'] = $user_id;
		
		// 送信データの設定
		$this->set_response_array($result);
		
		Log::DebugOut("Game_main::index() end");
	}
	
	public function mode_select()
	{
		Log::DebugOut("Game_main::mode_select()");
		
		$result = array();
		
		$user_id = 0;
		$ticket_num = 0;
		$select_mode_id = -1;
		
		// 選択されたモードID
		$mode_id = $this->input->post('MODE_ID');
		
		// 最大可能選択数
		$max_mode_id = $this->config->item('MAX_MODE_ID');
		
		// モードデータ
		$mode_info = $this->config->item('mode_info');
		
		if (($mode_id !== FALSE) && (0 <= $mode_id) && ($mode_id < $max_mode_id)) {
		
			// UIDの取得
			$uid = $this->config->item('UID');
			
			$user_info = $this->user_model->is_user_id($uid);
			Log::ObjectDataOut($user_info);
			
			if ($user_info !== FALSE) {
				
				$user_id = $user_info['USER_ID'];
				
				// チケット所持数
				$ticket_num = $user_info['TICKET_NUM'];
				
				if ($mode_id == MODE_A) {
					$select_mode_id = MODE_A;
				}
				// それ以外はチケットが必要
				else if (0 < $mode_id) {
				
					// チケット枚数チェック
					$need_ticket = $mode_info[$mode_id];
					$cosume_ticket = $need_ticket * -1;
					
					Log::DebugOut("ticket_num:".$ticket_num.", need_ticket:".$need_ticket);
					
					if ($need_ticket <= $ticket_num) {
						
						// 選択されたモードID
						$select_mode_id = $mode_id;
						
						// 消費処理
						$this->user_model->update_user_ticket_num($user_id, $cosume_ticket);
						
						// 消費ログ作成
						$this->user_model->insert_consume_log($user_id, $mode_id, $mode_info[$mode_id]);
						
						$ticket_num += $cosume_ticket;
					}
				}
				
				if ($select_mode_id != -1) {
					
					// 初期化データ作成
					$this->record_model->insert_mode_clear($user_id, $select_mode_id);
				}
			}
		}
		
		$result['USER_ID'] = $user_id;
		$result['TICKET_NUM'] = $ticket_num;
		$result['MODE_ID'] = $select_mode_id;
		
		// 送信データの設定
		$this->set_response_array($result);
		
		Log::DebugOut("Game_main::mode_select() end");
	}
	
	public function stage_end()
	{
		Log::DebugOut("Game_main::stage_end()");
		
		$result = array();
		$user_id = 0;
		$mode_clear_id = 0;
		$high_score_flag = 0;
		$check_score = 0;
		
		// モードID
		$mode_id = $this->input->post('MODE_ID');
		
		// ステージID
		$stage_id = $this->input->post('STAGE_ID');
		
		// ステージ内での獲得スコア
		$score = $this->input->post('SCORE');
		
		// ステージ内での経過タイム
		$time = $this->input->post('TIME');
		
		// UIDの取得
		$uid = $this->config->item('UID');
		
		$user_info = $this->user_model->is_user_id($uid);
		Log::ObjectDataOut($user_info);
		
		if ($user_info !== FALSE) {
			
			$user_id = $user_info['USER_ID'];
			$best_high_score = $user_info['BEST_HIGH_SCORE'];
			
			// 最新のモードクリア情報の取得
			$mode_clear_id = $this->record_model->get_latest_mode_clear($user_id, $mode_id);
			
			if ($mode_clear_id != 0) {
				
				// モードクリア情報の詳細取得
				$mode_clear_info = $this->record_model->get_mode_clear($mode_clear_id);
				
				// ここまでのスコア
				$check_score = $mode_clear_info['SCORE'];
				
				// 今回のスコアを加算
				$check_score += $score;
				
				// ステージクリア情報の新規作成
				$this->record_model->insert_stage_clear($user_id, $stage_id, $mode_clear_id, $score, $time);
				
				// モードクリア情報の更新
				$this->record_model->update_mode_clear($mode_clear_id, STAGE_RESULT, $score, $time);
				
				// ハイスコアデータのチェック
				$high_score_flag = $this->record_model->check_update_high_score($user_id, $mode_id, $check_score);
				
				// ハイスコアを更新している場合
				if ($high_score_flag !== 0) {
					$this->user_model->check_best_high_score($user_id, $best_high_score, $check_score);
				}
			}
		}
		
		$result['USER_ID'] = $user_id;
		$result['HIGH_SCORE'] = $high_score_flag;
		
		// 送信データの設定
		$this->set_response_array($result);
		
		Log::DebugOut("Game_main::stage_end() end");
	}
	
	public function mode_end()
	{
		Log::DebugOut("Game_main::mode_end()");
		
		$result = array();
		$user_id = 0;
		
		// モードの終了状況
		$state = $this->input->post('STATE');
		
		// UIDの取得
		$uid = $this->config->item('UID');
		
		$user_info = $this->user_model->is_user_id($uid);
		Log::ObjectDataOut($user_info);
		
		if ($user_info !== FALSE) {
			
			$user_id = $user_info['USER_ID'];
			
			// 最新のモードクリア情報の取得
			$mode_clear_id = $this->record_model->get_latest_mode_clear($user_id);
			
			if ($mode_clear_id != 0) {
				
				// 詳細情報の取得
				$mode_clear_info = $this->record_model->get_mode_clear($mode_clear_id);
				
				if ($mode_clear_info !== FALSE) {
				
					$mode_id = $mode_clear_info['MODE_ID'];
					$score = $mode_clear_info['SCORE'];
					
					// モードクリア情報の更新_フラグのみ
					$this->record_model->update_mode_clear($mode_clear_id, $state);
					
					// 称号の取得チェック
					$this->results_model->check_clear_results($user_id, $mode_id);
					$this->results_model->check_score_results($user_id, $mode_id, $score);
					
					// プレイ回数
					$play_count = $user_info['PLAY_COUNT'];
					
					// プレイ回数更新
					$this->user_model->update_user_play_info($user_id, $score);
					
					// プレイ回数称号チェック
					$this->results_model->check_play_count_results($user_id, ($play_count + 1));
				}
			}
		}
		
		$result['USER_ID'] = $user_id;
		
		// 送信データの設定
		$this->set_response_array($result);
		
		Log::DebugOut("Game_main::mode_end() end");
	}
	
}

/* End of file game_main.php */
/* Location: ./application/controllers/game_main.php */