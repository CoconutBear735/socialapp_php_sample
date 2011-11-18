<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends E_Model {

	function User_model()
	{
		parent::__construct();
	}
	
	//-------------------------------------------------------------------------------------
	// MT_USER�ėp�t�B�[���h���상�\�b�h
	//-------------------------------------------------------------------------------------	
	
	// ���[�U�[ID���o�^����Ă��邩�ǂ���
	function is_user_id($uid = '')
	{
		Log::DebugOut("UM::is_user_id(uid:".$uid.")");
		
		// UID�������Ă��Ȃ��ꍇ�A���̏�Ŏ擾
		if (strlen(trim($uid)) == 0) {
			$uid = $this->config->item('UID');
		}
		
		if (0 < strlen(trim($uid))) {
		
			// USER_ID�����擾
			$this->db->select('PLATFORM_ID, USER_ID, CARRIER, GALLERY_FLAG, INSPECTION_DATE');
			$this->db->from('MT_USER');
			$this->db->where('UID', $uid);
			
			$query = $this->db->get();
			
			$row = array();
			
			// �擾�������g������ꍇ
			if (0 < $query->num_rows()) {
			
				$row = $query->row_array();
				
				return $row;
			}
		}
		
		return FALSE;
	}
	
	// ���[�U�[�o�^
	function insert_user_data($uid = '')
	{
		Log::DebugOut("UM::insert_user_data(uid:".$uid.")");
		
		$user_id = 0;
		
		// UID�������Ă��Ȃ��ꍇ�A���̏�Ŏ擾
		if (strlen(trim($uid)) == 0) {
			$uid = $this->config->item('UID');
		}
		
		//mixi�V�KID�擾�`�F�b�N
		$personal_info = $this->mixi_request->get_personal_info($uid, NULL);
		
		if (0 < strlen(trim($uid))) {
			
			$carrier = 0;
			$model = '';
			
			$now = date('Y-m-d H:i:s', time());
			
			// �f�o�C�X�^�C�v
			$device = $this->config->item('DEVICE');
			
			// �g�т̏ꍇ
			if ($device == 1) {
			
				$carrier = $this->config->item('CARRIER');
				$model =  $this->config->item('MODEL');
			}
			
			// �V�K�쐬�̃f�[�^�i�[
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
			
			// user_id���擾
			$user_id = $this->db->insert_id();
		}
		
		return $user_id;
	}
	
	// ���[�U�[���ύX
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
	
	// �[�����̍X�V�֐�
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
	
	// ���[�U�[ID�擾�֐�
	function get_user_id_by_uid($uid = '')
	{
		Log::DebugOut("UM::get_user_id_by_uid(uid:".$uid.")");
		
		// �z��`�F�b�N
		if (!is_array($uid)) {
		
		    // UID�������Ă��Ȃ��ꍇ�A���̏�Ŏ擾
		    if (strlen(trim($uid)) == 0) {
			    $uid = $this->config->item('UID');
		    }
		    
		    $user_id = 0;
		    
		    if (0 < strlen(trim($uid))) {
			    
			    // USER_ID�����擾
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
	// �X�e�[�W�N���AID���상�\�b�h
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
	
	// mixiID���X�g�̑Ώۃ��[�U����X�e�[�W�N���A�֘A�����擾����
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
	
	// �ő�N���A�X�e�[�W�f�[�^ID�̍X�V�֐��@
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
	
	// ϲи�̃��[�UID�����擾
	function get_friend_info($uid = '', $json = FALSE)
	{
		Log::DebugOut("get_friend_info(uid:".$uid.", json:".$json.")");
		
		// �}�C�~�N���擾
		$friend_info = $this->mixi_request->get_friends($uid, TRUE);
		
		$result = FALSE;
		
		if (array_key_exists('entry', $friend_info)) {
		
			// �}�C�~�N��
			$entry_info = $friend_info['entry'];
			$friend_count = count($entry_info);
			
			Log::DebugOut("friend_count:".$friend_count);
			
			$result = array();
			
			// �}�C�~�NID�̂ݎ��o��
			foreach ($entry_info as $key => $val) {
			
				// mixi_id����mixi.jp:��؂蔲��
				$str_id = $val['id'];
				$id = substr($str_id, 8);
				
				// �}�C�~�N��mixiID�z��
				$friend_uid_array[$key] = $id;
				
				// ���O�y�уT���l�C��URI�z��
				$name_array[$id]  = $val['displayName'];
				$image_array[$id] = $val['thumbnailUrl'];
			}
			
			// �}�C�~�N��USER_ID�y��STAGE_CLEAR_ID�f�[�^���擾
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
		
		// ����������UID����USER_ID�ɕύX
		// USER_ID��UID�ɕR�t���Ď擾
		// �L���b�V���ɓ����ĂȂ��ꍇ�́A�P��A�N�Z�X�������Ȃ�
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
	
	// �Z�b�V����ID�̎擾
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
