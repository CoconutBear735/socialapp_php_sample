<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends E_Model {

	function User_model()
	{
		parent::__construct();
	}
	
	//-------------------------------------------------------------------------------------
	// MT_USER汎用フィールド操作メソッド
	//-------------------------------------------------------------------------------------	
	
	// ユーザーIDが登録されているかどうか
	function is_user_id($uid = '')
	{
		Log::DebugOut("UM::is_user_id(uid:".$uid.")");
		
		// UIDが入っていない場合、その場で取得
		if (strlen(trim($uid)) == 0) {
			$uid = $this->config->item('UID');
		}
		
		if (0 < strlen(trim($uid))) {
		
			// USER_IDだけ取得
			$this->db->select('PLATFORM_ID, USER_ID, CARRIER, GALLERY_FLAG, INSPECTION_DATE');
			$this->db->from('MT_USER');
			$this->db->where('UID', $uid);
			
			$query = $this->db->get();
			
			$row = array();
			
			// 取得した中身がある場合
			if (0 < $query->num_rows()) {
			
				$row = $query->row_array();
				
				return $row;
			}
		}
		
		return FALSE;
	}
	
	// ユーザー登録
	function insert_user_data($uid = '')
	{
		Log::DebugOut("UM::insert_user_data(uid:".$uid.")");
		
		$user_id = 0;
		
		// UIDが入っていない場合、その場で取得
		if (strlen(trim($uid)) == 0) {
			$uid = $this->config->item('UID');
		}
		
		//mixi新規ID取得チェック
		$personal_info = $this->mixi_request->get_personal_info($uid, NULL);
		
		if (0 < strlen(trim($uid))) {
			
			$carrier = 0;
			$model = '';
			
			$now = date('Y-m-d H:i:s', time());
			
			// デバイスタイプ
			$device = $this->config->item('DEVICE');
			
			// 携帯の場合
			if ($device == 1) {
			
				$carrier = $this->config->item('CARRIER');
				$model =  $this->config->item('MODEL');
			}
			
			// 新規作成のデータ格納
			$params = array(
				'UID'          => $uid,
				'ACT_FLAG'     => 1,
				'CARRIER'      => $carrier,
				'AGENT'        => $model,
				'START_DEVICE' => $device,
				'CREATE_DATE'  => $now,
				'UPDATE_DATE'  => $now
			);
			
			// INSERT
			$this->db->insert('MT_USER', $params);
			
			// user_idを取得
			$user_id = $this->db->insert_id();
		}
		
		return $user_id;
	}
	
	// ユーザー情報変更
	function update_user_data($user_id = '')
	{
		Log::DebugOut("UM::update_user_data(user_id:".$user_id.")");
		
		if ($user_id < 0) {
			return FALSE;
		}
			
		$params = array(
			'ACT_FLAG'    => 1,
			'UPDATE_DATE' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('USER_ID', $user_id);
		$this->db->update('MT_USER', $params);
	}
	
	// 端末情報の更新関数
	function update_device_info($user_id = 0, $carrier = 0, $agent = '')
	{
		Log::DebugOut("UM::update_device_info(user_id:".$user_id.", carrier:".$carrier.", agent:".$agent.")");
		
		if (($user_id <= 0) || ($carrier <= 0)) {
			return FALSE;
		}
		
		$params = array(
			'CARRIER'     => $carrier,
			'AGENT'       => $agent,
			'UPDATE_DATE' => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('USER_ID', $user_id);
		$this->db->update('MT_USER', $params);		
	}
	
	// ユーザーID取得関数
	function get_user_id_by_uid($uid = '')
	{
		Log::DebugOut("UM::get_user_id_by_uid(uid:".$uid.")");
		
		// 配列チェック
		if (!is_array($uid)) {
		
		    // UIDが入っていない場合、その場で取得
		    if (strlen(trim($uid)) == 0) {
			    $uid = $this->config->item('UID');
		    }
		    
		    $user_id = 0;
		    
		    if (0 < strlen(trim($uid))) {
			    
			    // USER_IDだけ取得
			    $this->db->select('USER_ID');
			    $this->db->from('MT_USER');
			    $this->db->where('UID', $uid);
				
			    $query = $this->db->get();
			    
			    $row = array();
			    
			    if (0 < $query->num_rows()) {
			    
				    $row = $query->row_array();
				    
				    $user_id = $row['USER_ID'];
			    }
		    }
		    
		    Log::DebugOut("user_id:".$user_id);
		    
		    return $user_id;
		}
		else {
			
			$this->db->select('PLATFORM_UID, USER_ID');
			$this->db->from('MT_USER');
			$this->db->where_in('UID', $uid);
			
			$query = $this->db->get();
			
			$result = array();
			
			if (0 < $query->num_rows()) {
				$result = $query->result_array();
			}
			
			Log::ObjectDataOut($result);
			
			return $result;
		}
	}
	
	//-------------------------------------------------------------------------------------
	// ステージクリアID操作メソッド
	//-------------------------------------------------------------------------------------	
	
	function get_stage_clear_id($user_id = 0)
	{
		Log::DebugOut("UM::get_stage_clear_id(user_id:".$user_id.")");
		
		if ($user_id <= 0) {
			return FALSE;
		}
		
		$stage_clear_id = 0;
		
		$this->db->select('STAGE_CLEAR_ID');
		$this->db->from('MT_USER');
		$this->db->where('USER_ID', $user_id);
		
		$query = $this->db->get();
		
		$row = array();
		
		if (0 < $query->num_rows()) {
			$row = $query->row_array();
			$stage_clear_id = $row['STAGE_CLEAR_ID'];
		}
		
		Log::DebugOut("stage_clear_id:".$stage_clear_id);
		
		return $stage_clear_id;
	}
	
	// mixiIDリストの対象ユーザからステージクリア関連情報を取得する
	function get_stage_clear_info($uid_array = array())
	{
		Log::DebugOut("UM::get_stage_clear_info()");
		Log::ObjectDataOut($uid_array);
		
		if (!has_value($uid_array)) {
			return FALSE;
		}
		
		$this->db->select('PLATFORM_ID, UID, USER_ID, STAGE_CLEAR_ID');
		$this->db->from('MT_USER');
		$this->db->where_in('UID', $uid_array);
		
		$query = $this->db->get();
		
		$result = array();
		
		if (0 < $query->num_rows()) {
			$result = $query->result_array();
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	// 最大クリアステージデータIDの更新関数　
	function update_user_stage_clear_id($user_id = 0, $stage_clear_id = 0)
	{
		Log::DebugOut("UM::update_user_stag_clear_id(user_id:".$user_id.", stage_clear_id:".$stage_clear_id.")");
		
		if (($user_id == 0) || ($stage_clear_id == 0)) {
			return FALSE;
		}
		
		$params = array(
			'STAGE_CLEAR_ID' => $stage_clear_id,
			'UPDATE_DATE'    => date('Y-m-d H:i:s', time())
		);
		
		$this->db->where('USER_ID', $user_id);
		$this->db->update('MT_USER', $params);
	}
	
	// ﾏｲﾐｸのユーザID情報を取得
	function get_friend_info($uid = '', $json = FALSE)
	{
		Log::DebugOut("get_friend_info(uid:".$uid.", json:".$json.")");
		
		// マイミク情報取得
		$friend_info = $this->mixi_request->get_friends($uid, TRUE);
		
		$result = FALSE;
		
		if (array_key_exists('entry', $friend_info)) {
		
			// マイミク数
			$entry_info = $friend_info['entry'];
			$friend_count = count($entry_info);
			
			Log::DebugOut("friend_count:".$friend_count);
			
			$result = array();
			
			// マイミクIDのみ取り出し
			foreach ($entry_info as $key => $val) {
			
				// mixi_idからmixi.jp:を切り抜く
				$str_id = $val['id'];
				$id = substr($str_id, 8);
				
				// マイミクのmixiID配列
				$friend_uid_array[$key] = $id;
				
				// 名前及びサムネイルURI配列
				$name_array[$id]  = $val['displayName'];
				$image_array[$id] = $val['thumbnailUrl'];
			}
			
			// マイミクのUSER_ID及びSTAGE_CLEAR_IDデータを取得
			$friend_user_info = $this->get_stage_clear_info($friend_uid_array);
			
			foreach ($friend_user_info as $key => $val) {
				
				$result[$key] = array();
				
				$friend_uid     = $val['PLATFORM_ID'];
				$friend_user_id = $val['USER_ID'];
				
				$result[$key]['MIXI_ID']        = $friend_uid;
				$result[$key]['USER_ID']        = $friend_user_id;
				$result[$key]['NAME']           = $name_array[$friend_uid];
				$result[$key]['IMAGE_URL']      = $image_array[$friend_uid];
				$result[$key]['STAGE_CLEAR_ID'] = $val['STAGE_CLEAR_ID'];
			}
		}
		
		Log::ObjectDataOut($result);
		
		return $result;
	}
	
	function valid_req($uid = '', $sid = '')
	{
		Log::DebugOut("UM::valid_req(uid:".$uid.", sid:".$sid.")");
		
		if (!empty($sid)) {
		
		// 検索条件をUIDからUSER_IDに変更
		// USER_IDはUIDに紐付けて取得
		// キャッシュに入ってない場合は、１回アクセスが多くなる
		//	$ckey = 'user_uid_'.$uid;
		//	$result = $this->memcache->get($ckey);
		//if ($result === FALSE) {
		
			$user_id = $this->get_user_id_by_uid($uid);
			
			$this->db->select('PLATFORM_ID, UID, SID');
			$this->db->where('USER_ID', $user_id);
			
			$query = $this->db->get('MT_USER');
			
			if ($query->num_rows() > 0) {
			
				$result = $query->row_array();
				
				$query->free_result();
			}
		}
		
		Log::DebugOut("QUERY_SID:".$sid);
		Log::DebugOut("   DB_SID:".$result['SID']);

		if (!empty($result['SID']) && $result['SID'] == $sid) {
			return TRUE;
		}
		
		//}
		
		return FALSE;
	}
	
	function update_req_user_id($user_id = 0, $sid = '')
	{
		Log::DebugOut("UM::update_req_user_id(user_id:".$user_id.", sid:".$sid.")");
		
		$this->db->where('USER_ID', $user_id);
		$this->db->update('MT_USER', array('SID' => $sid));
	}
	
	function update_req($uid = '', $sid = '')
	{
		Log::DebugOut("UM::update_req(uid:".$uid.", sid:".$sid.")");
		
		$this->db->where('PLATFORM_ID', $uid);
		//$this->db->where('UID', $uid);
		$this->db->update('MT_USER', array('SID' => $sid));
	}
	
	// セッションIDの取得
	function get_req($uid = '', $sid = '')
	{
		Log::DebugOut("UM::get_req(uid:".$uid.", sid:".$sid.")");
		
		$this->db->select('SID');
		$this->db->where('UID', $uid);
		
		$query = $this->db->get('MT_USER');
		
		if ($query->num_rows() > 0) {
		
			$row = $query->row_array();
			
			if (empty($row['SID'])) {
			
				if (!empty($sid)) {
				
					$this->update_req($uid, $sid);
					
					return $sid;
				}
				
				return FALSE;
			}
			
			return $row['SID'];
		}
		
		return FALSE;
	}
	
	//-------------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------------
}

/* End of file user_model.php */
/* Location: ./model/user_model.php */
