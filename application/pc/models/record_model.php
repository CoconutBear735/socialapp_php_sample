<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("include->record_model.php");

class Record_model extends E_Model {

	// コンストラクタ
	function Record_model()
	{
		parent::__construct();
	}
	
	// モードクリアデータの新規作成
	function insert_mode_clear($user_id = 0, $mode_id = 0, $ticket_id = 0)
	{
		Log::DebugOut("REM::insert_mode_clear(user_id:".$user_id.", mode_id:".$mode_id.", ticket_id:".$ticket_id.")");
		
		if (($user_id <= 0) || ($mode_id < 0)) {
			return FALSE;
		}
		
		$mode_clear_id = 0;
		
		$params = array(
			'USER_ID'     => $user_id,
			'MODE_ID'     => $mode_id,
			'STATE'       => 0,
			'TICKET_ID'   => $ticket_id,
			'CREATE_DATE' => date('Y-m-d H:i:s'),
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);
		
		$this->db->insert('TR_MODE_CLEAR', $params);
		
		$mode_clear_id = $this->db->insert_id();
		
		return $mode_clear_id;
	}
	
	// モードクリアデータの新規作成
	function insert_stage_clear($user_id = 0, $stage_id = 0, $mode_clear_id = 0, $score = 0, $time = 0)
	{
		Log::DebugOut("REM::insert_stage_clear(user_id:".$user_id.", stage_id:".$stage_id.", mode_clear_id:".$mode_clear_id.", score:".$score.", time:".$time.")");
		
		if (($user_id <= 0) || ($stage_id <= 0)) {
			return FALSE;
		}
		
		$stage_clear_id = 0;
		
		$params = array(
			'USER_ID'       => $user_id,
			'STAGE_ID'      => $stage_id,
			'MODE_CLEAR_ID' => $mode_clear_id,
			'SCORE'         => $score,
			'TIME'          => $time,
			'CREATE_DATE'   => date('Y-m-d H:i:s'),
			'UPDATE_DATE'   => date('Y-m-d H:i:s')
		);
		
		$this->db->insert('TR_STAGE_CLEAR', $params);
		
		$stage_clear_id = $this->db->insert_id();
		
		return $stage_clear_id;
	}
	
	function get_mode_clear($mode_clear_id = 0)
	{
		Log::DebugOut("REM::get_mode_clear(mode_clear_id:".$mode_clear_id.")");
		
		if ($mode_clear_id <= 0) {
			return FALSE;
		}
		
		$row = FALSE;
		
		$this->db->select('MODE_ID, STATE, SCORE, TIME');
		$this->db->from('TR_MODE_CLEAR');
		
		$this->db->where('MODE_CLEAR_ID', $mode_clear_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			$row = $query->row_array();
			$query->free_result();
		}
		
		Log::ObjectDataOut($row);
		
		return $row;
	}
	
	function get_latest_mode_clear($user_id = 0, $mode_id = -1)
	{
		Log::DebugOut("REM::get_latest_mode_clear(user_id:".$user_id.", mode_id:".$mode_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$row = array();
		$max_mode_id = 0;
		
		$this->db->select('MAX(MODE_CLEAR_ID) as max_id');
		$this->db->from('TR_MODE_CLEAR');
		
		$this->db->where('USER_ID', $user_id);
		
		if (0 <= $mode_id) {
			$this->db->where('MODE_ID', $mode_id);
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			$row = $query->row_array();
			$query->free_result();
			
			$max_mode_id = $row['max_id'];
		}
		
		Log::DebugOut("max_mode_id:".$max_mode_id);
		
		return $max_mode_id;
	}
	
	// モードクリアデータ更新
	function update_mode_clear($mode_clear_id = 0, $state = 0, $score = 0, $time = 0)
	{
		Log::DebugOut("REM::update_mode_clear(mode_clear_id:".$mode_clear_id.", state:".$state.", score:".$score.", time:".$time.")");
		
		if ($mode_clear_id <= 0) {
			return FALSE;
		}
		
		$this->db->query('UPDATE TR_MODE_CLEAR SET STATE = '.$state.', SCORE = SCORE + '.$score.', TIME = TIME + '.$time.', UPDATE_DATE = \''.date('Y-m-d H:i:s', time()).'\' WHERE MODE_CLEAR_ID = '.$mode_clear_id);
	}
	
	// 日別ハイスコアデータの新規作成
	function insert_own_high_score($user_id = 0, $mode_id = 0, $high_score = 0)
	{
		Log::DebugOut("REM::insert_own_high_score(user_id:".$user_id.", mode_id:".$mode_id.", high_score:".$high_score.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$own_high_score_id = 0;
		
		$params = array(
			'USER_ID'     => $user_id,
			'MODE_ID'     => $mode_id,
			'HIGH_SCORE'  => $high_score,
			'CREATE_DATE' => date('Y-m-d H:i:s'),
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);
		
		$this->db->insert('TR_OWN_HIGH_SCORE', $params);
		
		$own_high_score_id = $this->db->insert_id();
		
		return $own_high_score_id;
	}
	
	// 本日のハイスコアデータの取得
	function get_today_high_score($user_id = 0)
	{
		Log::DebugOut("REM::get_today_high_score(user_id:".$user_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$today = date("Y-m-d");
		
		$row = FALSE;
		
		$this->db->select('OWN_HIGH_SCORE_ID, MODE_ID, HIGH_SCORE');
		$this->db->from('TR_OWN_HIGH_SCORE');
		
		$this->db->where('USER_ID', $user_id);
		$this->db->where('DATE(CREATE_DATE)', $today);
		
		$query = $this->db->get();
		
		// 取得した中身がある場合
		if ($query->num_rows() > 0) {
			
			$row = $query->row_array();
			$query->free_result();
		}
		
		Log::ObjectDataOut($row);
		
		return $row;
	}
	
	function update_today_high_score($own_high_score_id = 0, $mode_id = 0, $high_score = 0)
	{
		Log::DebugOut("REM::update_today_high_score(own_high_score_id:".$own_high_score_id.", mode_id:".$mode_id.", high_score:".$high_score.")");
		
		if ($own_high_score_id <= 0) {
			return FALSE;
		}
		
		$this->db->query('UPDATE TR_OWN_HIGH_SCORE SET HIGH_SCORE = '.$high_score.', MODE_ID = '.$mode_id.', UPDATE_DATE = \''.date('Y-m-d H:i:s', time()).'\' WHERE OWN_HIGH_SCORE_ID = '.$own_high_score_id);
		
		return 1;
	}
	
	function check_update_high_score($user_id = 0, $mode_id = 0, $score = 0)
	{
		Log::DebugOut("REN::check_update_high_score(user_id:".$user_id.", mode_id:".$mode_id.", score:".$score.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$update_flag = 0;
		
		// 本日のハイスコアデータを取得
		$today_high_score = $this->get_today_high_score($user_id);
		
		// 本日のデータが既に作成済みの場合
		if ($today_high_score !== FALSE) {
		
			$check_score = $today_high_score['HIGH_SCORE'];
			
			// 今回の値と比較
			if ($check_score <= $score) {
			
				$own_high_score_id = $today_high_score['OWN_HIGH_SCORE_ID'];
				
				$update_flag = $this->update_today_high_score($own_high_score_id, $mode_id, $score);
			}
		}
		// 本日のデータが未作成
		else {
		
			// 今回のスコアでデータ作成
			$update_flag = $this->insert_own_high_score($user_id, $mode_id, $score);
		}
		
		return $update_flag;
	}
	
	function get_two_week_score($user_id = 0)
	{
		Log::DebugOut("REM::get_two_week_score(user_id:".$user_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$result = FALSE;
		
		$today = date("Y-m-d");
		
		// ２週間前
		$two_weeks_ago = $this->computeDate(-14, FALSE, FALSE);
		Log::DebugOut("two_weeks_ago:".$two_weeks_ago);
		
		$this->db->select('CREATE_DATE, MODE_ID, HIGH_SCORE');
		$this->db->from('TR_OWN_HIGH_SCORE');
		
		$this->db->where('USER_ID', $user_id);
		$this->db->where('DATE(CREATE_DATE) >=', $two_weeks_ago);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			$result = $query->result_array();
			$query->free_result();
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	function get_max_stage_id($user_id = 0)
	{
		Log::DebugOut("REM::get_max_stage_id(user_id:".$user_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$row = array();
		$max_stage_id = 1;
		
		$this->db->select('MAX(STAGE_ID) as max_stage_id');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where('USER_ID', $user_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			$row = $query->row_array();
			$query->free_result();
			
			// 最大クリアステージID
			$max_stage_id = $row['max_stage_id'];
		}
		
		Log::DebugOut("max_stage_id:".$max_stage_id);
		
		return $max_stage_id;
	}

}

/* End of file record_model.php */
/* Location: ./model/record_model.php */
