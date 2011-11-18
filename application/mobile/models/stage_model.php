<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("include->stage_model.php");

class Stage_model extends E_Model {

	function Stage_model()
	{
		parent::__construct();
	}
	
	function insert_stage_clear($user_id = 0, $stage_id = 0)
	{
		Log::DebugOut("SM::insert_stage_clear(user_id:".$user_id." stage_id:".$stage_id.")");
		
		if (($user_id <= 0) || ($stage_id <= 0)) {
			return FALSE;
		}
		
		$stage_clear_id = 0;
		
		// 新規作成のデータ格納
		$params = array(
			'USER_ID'     => $user_id,
			'STAGE_ID'    => $stage_id,
			'CREATE_DATE' => date('Y-m-d H:i:s'),
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);
		
		// INSERT
		$this->db->insert('TR_STAGE_CLEAR', $params);
		
		$stage_clear_id = $this->db->insert_id();
		
		return $stage_clear_id;
	}
	
	function update_stage_clear($stage_clear_id = 0)
	{
		Log::DebugOut("SM::update_stage_clear(stage_clear_id:".$stage_clear_id.")");
		
		if ($stage_clear_id <= 0) {
			return FALSE;
		}
		
		$this->db->query('UPDATE TR_STAGE_CLEAR SET CLEAR_COUNT = CLEAR_COUNT + 1, UPDATE_DATE = \''.date('Y-m-d H:i:s', time()).'\' WHERE STAGE_CLEAR_ID = '.$stage_clear_id);
	}
	
	// 最大ステージクリアID取得
	function count_stage_clear($user_id = 0)
	{
		Log::DebugOut("SM::count_stage_clear(user_id:".$user_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$stage_clear_count = 0;
		
		$this->db->select('COUNT(STAGE_CLEAR_ID) as count');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where('USER_ID', $user_id);
		
		$query = $this->db->get();
		
		if (0 < $query->num_rows()) {
		
			$row = $query->row_array(); 
			$query->free_result();
			
			$stage_clear_count = $row['count'];
		}
		
		Log::DebugOut("stage_clear_count:".$stage_clear_count);
		
		return $stage_clear_count;
	}
	
	function get_stage_clear_id($user_id = 0, $stage_id = 0)
	{
		Log::DebugOut("SM::get_stage_clear_iduser_id:".$user_id." stage_id:".$stage_id.")");
		
		if (($user_id <= 0) || ($stage_id <= 0)) {
			return FALSE;
		}
		
		$stage_clear_id = 0;
		
		$this->db->select('STAGE_CLEAR_ID');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where('USER_ID', $user_id);
		$this->db->where('STAGE_ID', $stage_id);
		
		$query = $this->db->get();
		
		if (0 < $query->num_rows()) {
		
			$row = array();
			
			$row = $query->row_array(); 
			$query->free_result();
			
			$stage_clear_id = $row['STAGE_CLEAR_ID'];
		}
		
		return $stage_clear_id;
	}
	
	function get_max_stage_id($user_id = 0)
	{
		Log::DebugOut("SM::get_max_stage_id(user_id:".$user_id.")");
		
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
	
	// --- 現在までのクリアデータを取得
	// stage_id : 0以下 ---> FALSE
	// stage_id : 0     ---> 全てのデータ
	// stage_id : 0以上 ---> そのステージのデータ
	function get_stage_clear($user_id = 0, $stage_id = 0, $sort_type = SORT_STAGE_ID)
	{
		Log::DebugOut("SM::get_stage_clear(user_id:".$user_id.", stage_id:".$stage_id.", sort_type:".$sort_type.")");
		
		if (($user_id <= 0) || ($stage_id < 0)) {
			return FALSE;
		}
		
		$result = FALSE;
		
		$this->db->select('STAGE_ID, UPDATE_DATE');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where('USER_ID', $user_id);
		
		if (0 < $stage_id) {
			$this->db->where('STAGE_ID', $stage_id);
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			if (0 < $stage_id) {
			
				$result = $query->row_array();
				$query->free_result();
			}
			// 全体を取得する場合はソートを掛ける
			else {
			
				$sort_array = array();
				
				$result = $query->result_array();
				$query->free_result();
				
				if ($sort_type == SORT_STAGE_ID) {
				
					foreach($result as $key => $val) {
						$sort_array[$key] = $val['STAGE_ID'];
					}
					
					array_multisort($sort_array, SORT_ASC, $result);
				}
				else if ($sort_type == SORT_UPDATE_DATE) {
				
					foreach($result as $key => $val) {
						$sort_array[$key] = $val['UPDATE_DATE'];
					}
					
					array_multisort($sort_array, SORT_DESC, $result);
				}
				
			}
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	function get_stage_clear_array($stage_clear_id_array = array())
	{
		Log::DebugOut("SM::get_stage_clear_array()");
		Log::ObjectDataOut($stage_clear_id_array);
		
		if (!has_value($stage_clear_id_array)) {
			return FALSE;
		}
		
		$this->db->select('USER_ID, STAGE_ID, DATE(UPDATE_DATE) AS date');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where_in('STAGE_CLEAR_ID', $stage_clear_id_array);
		
		$query = $this->db->get();
		
		$result = FALSE;
		
		if (0 < $query->num_rows()) {
			$result = $query->result_array();
			$query->free_result();
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	function get_max_stage_clear_id($user_id_array = array())
	{
		Log::DebugOut("SM::get_max_stage_clear_id()");
		Log::ObjectDataOut($user_id_array);  
		
		if (!has_value($user_id_array)) {
			return FALSE;
		}
		
		$this->db->select('USER_ID, COUNT(STAGE_CLEAR_ID) as count');
		$this->db->from('TR_STAGE_CLEAR');
		$this->db->where_in('USER_ID', $user_id_array);
		$this->db->group_by('USER_ID');
		
		$query = $this->db->get();
		
		$result = FALSE;
		
		if ($query->num_rows() > 0) {
			
			$sort_array = array();
			
			$result = $query->result_array();
			$query->free_result();
			
			foreach($result as $key => $val) {
				$sort_array[$key] = $val['STAGE_ID'];
			}
			
			// 降順
			array_multisort($sort_array, SORT_DESC, $result);
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	function make_friend_ranking($ranking_user_info = array())
	{
		Log::DebugOut("make_friend_ranking()");
		Log::ObjectDataOut($ranking_user_info);
		
		$src_array = array();
		$stage_clear_id_array = array();
		$friend_ranking_array = array();
		
		foreach ($ranking_user_info as $key => $val) {
		
			$stage_clear_id = $val['STAGE_CLEAR_ID'];
			
			if (abs($stage_clear_id) != 0) {
				$stage_clear_id_array[] = $stage_clear_id;
			}
			else {
			
				$temp['USER_ID']  = $val['USER_ID'];
				$temp['STAGE_ID'] = '0000000000';
				$temp['date']     = '0000-00-00';
				
				$src_array[] = $temp;
			}
		}
		
		if (has_value($stage_clear_id_array)) {
		
			$stage_clear_info = $this->get_stage_clear_array($stage_clear_id_array);
			
			$src_array = array_merge($src_array, $stage_clear_info);
			
			$stage_array = array();
			$date_array = array();
			
			// ソート用配列
			foreach ($src_array as $key => $val) {
				$stage_array[$key] = $val['STAGE_ID'];
				$date_array[$key] = $val['date'];
			}
			
			array_multisort($stage_array, SORT_DESC, $date_array, SORT_DESC, $src_array);
		}
		
		$rank = 1;
		
		foreach ($src_array as $key => $val) {
		
			$user_id = $val['USER_ID'];
			
			$friend_ranking_array[$key] = $src_array[$key];
			
			foreach ($ranking_user_info as $key2 => $val2) {
			
				if ($user_id == $val2['USER_ID']) {
					$friend_ranking_array[$key]['NAME']      = $val2['NAME'];
					$friend_ranking_array[$key]['IMAGE_URL'] = $val2['IMAGE_URL'];
				}
			}
			
			$friend_ranking_array[$key]['RANK']     = $rank;
			$friend_ranking_array[$key]['STAGE_ID'] = abs($friend_ranking_array[$key]['STAGE_ID']);
			
			$rank++;
			
			if (10 < $rank) {
				break 1;
			}
		}
		
		Log::ObjectDataOut($friend_ranking_array);
		
		return $friend_ranking_array;
	}
}

/* End of file stage_model.php */
/* Location: ./model/stage_model.php */
	