<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class E_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		// �f�[�^�x�[�X�Ăяo��
		$this->load->database();
	}
	
	// ���t�������v�Z
	function get_day_interval($now_day = FALSE, $last_day = FALSE)
	{
		Log::DebugOut("get_day_interval(now_day:".$now_day.", last_day:".$last_day.")");
		
		if (($now_day === FALSE) || ($last_day === FALSE)) {
			return FALSE;
		}
		
		if ($last_day === '0000-00-00') {
			return 1;
		}
		
		//���t��UNIX�^�C���X�^���v�ɕϊ�
		$now_time = strtotime($now_day);
		$last_time = strtotime($last_day);
		
		//���b����Ă��邩���v�Z(��Βl)
		$SecondDiff = abs($now_time - $last_time);
			
		$DayDiff = $SecondDiff / (60 * 60 * 24);
		
		return floor($DayDiff);
	}
	
	// ���������v�Z
	function get_last_month_interval($now_month = FALSE, $last_month = FALSE)
	{
		Log::DebugOut("get_last_month_interval(now_month:".$now_month.", last_month:".$last_month.")");
		
		if (($now_month === FALSE) || ($last_month === FALSE)) {
			return FALSE;
		}
		
		$nowdate = strtotime($now_month);
		$lastdate = strtotime($last_month);
		
		$month1 = date("Y", $nowdate) * 12 + date("m", $nowdate);
		$month2 = date("Y", $lastdate) * 12 + date("m", $lastdate);
		
		$diff = $month1 - $month2;
		
		Log::DebugOut("diff:".$diff);
		
		$result = -1;
		
		// ������
		if ($diff == 0) {
			$result = 0;
		}
		// �P�����O
		else if ($diff == 1) {
			$result = 1;
		}
		// ����ȑO
		else {}
		
		return $result;
	}
	
	// �P�����t�f�[�^�쐬�֐�
	function make_beginning_month($month = 0)
	{
		Log::DebugOut("make_beginning_month(month:".$month.")");
		
		// �P�Q���ȏ�͏Ȃ�
		if (12 < $month) {
			return FALSE;
		}
		
		// �^�C���X�^���v����ϊ�
		$time = time();
		$time = getdate($time);
		
		// �N�E�������o��
		$year = $time['year'];
		
		// �w�肪����ꍇ�͎w�茎
		if (0 < $month) {
			$mon = $month;
		}
		else {
			$mon = $time['mon'];
		}
		
		// ���̌��ƂP���݂̂ŃL�[���쐬
		$now_month = date('Y-m-d', mktime(0, 0, 0, $mon, 1, $year));
		
		return $now_month;
	}
	
	// �����O����t���v�Z
	function computeDate($addDays = 0, $date = FALSE)
	{
		Log::DebugOut("computeDate(addDays:".$addDays.", date:".$date.")");
		
		//$baseSec = mktime(0, 0, 0, $month, $day, $year);//�����b�Ŏ擾
		if ($date === FALSE) {
			$now_time = time();
		}
		else {
			$now_time = $date;
		}
		
		$addSec = $addDays * 86400; //�����~�P���̕b��
		
		$targetSec = $now_time + $addSec;
		
		return date("Y-m-d H:i:s", $targetSec);
	}
}

// END E_Model class

/* End of file E_Model.php */
/* Location: ./application/core/E_Model.php */
