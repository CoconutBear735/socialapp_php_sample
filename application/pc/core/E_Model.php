<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class E_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		// データベース呼び出し
		$this->load->database();
	}
	
	// 日付差分を計算
	function get_day_interval($now_day = FALSE, $last_day = FALSE)
	{
		Log::DebugOut("get_day_interval(now_day:".$now_day.", last_day:".$last_day.")");
		
		if (($now_day === FALSE) || ($last_day === FALSE)) {
			return FALSE;
		}
		
		if ($last_day === '0000-00-00') {
			return 1;
		}
		
		//日付をUNIXタイムスタンプに変換
		$now_time = strtotime($now_day);
		$last_time = strtotime($last_day);
		
		//何秒離れているかを計算(絶対値)
		$SecondDiff = abs($now_time - $last_time);
			
		$DayDiff = $SecondDiff / (60 * 60 * 24);
		
		return floor($DayDiff);
	}
	
	// 月差分を計算
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
		
		// 同じ月
		if ($diff == 0) {
			$result = 0;
		}
		// １ヶ月前
		else if ($diff == 1) {
			$result = 1;
		}
		// それ以前
		else {}
		
		return $result;
	}
	
	// １日日付データ作成関数
	function make_beginning_month($month = 0)
	{
		Log::DebugOut("make_beginning_month(month:".$month.")");
		
		// １２月以上は省く
		if (12 < $month) {
			return FALSE;
		}
		
		// タイムスタンプから変換
		$time = time();
		$time = getdate($time);
		
		// 年・月を取り出し
		$year = $time['year'];
		
		// 指定がある場合は指定月
		if (0 < $month) {
			$mon = $month;
		}
		else {
			$mon = $time['mon'];
		}
		
		// その月と１日のみでキーを作成
		$now_month = date('Y-m-d', mktime(0, 0, 0, $mon, 1, $year));
		
		return $now_month;
	}
	
	// ｎ日前後日付を計算
	function computeDate($addDays = 0, $date = FALSE)
	{
		Log::DebugOut("computeDate(addDays:".$addDays.", date:".$date.")");
		
		//$baseSec = mktime(0, 0, 0, $month, $day, $year);//基準日を秒で取得
		if ($date === FALSE) {
			$now_time = time();
		}
		else {
			$now_time = $date;
		}
		
		$addSec = $addDays * 86400; //日数×１日の秒数
		
		$targetSec = $now_time + $addSec;
		
		return date("Y-m-d H:i:s", $targetSec);
	}
}

// END E_Model class

/* End of file E_Model.php */
/* Location: ./application/core/E_Model.php */
