<?php
require_once(dirname(__FILE__) . "/Convert.php");
require_once(dirname(__FILE__) . "/Context.php");
require_once(dirname(__FILE__) . "/Utility.php");

// {{{ Emo_Decode

/**
 * デコード
 * 抽象絵文字コードから各社絵文字モバイルコードへ
 * 文字コードも変換
 */
class Emo_Decode
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_left_delimiter;
	var $_right_delimiter;
	var $_img_dir;
	var $_carrier;
	var $_carrier_v;
	var $_code_type;
	var $_auto_encode_data;
	var $_type_script;
	var $_utility;
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 *
	 * @param	context	$context	コンテキスト
	 */
	function Emo_Decode(&$context)
	{
		$this->_left_delimiter = $context->get("left_delimiter");
		$this->_right_delimiter = $context->get("right_delimiter");
		$this->_img_dir = $context->get("img_dir");
		$this->_carrier = $context->get("carrier");
		$this->_carrier_v = $context->get("carrier_v");
		$this->_code_type = $context->get("code_type");
		$this->_auto_encode_data = $context->get("auto_encode_data");
		$this->_type_script = $context->get("type_script");
		$this->_utility =& new Emo_Utility;
	}
	
	// }}}
	// {{{ main()
	
	/**
	 * デコード
	 * 抽象絵文字コードからモバイルコードへ（キャリアは自動判定）
	 * 文字コードも変換
	 *
	 * @param	string	$str
	 * @param  string  $option			1:タグも出力 0:モバイルコードのみ出力（docomoのみ対応）
	 * @return	string	$str
	 */
	function main($str, $option)
	{
		$str = $this->_strCodeDecode($str);			// 文字列文字コードデコード
		$str = $this->_decode($str, $option);		// 絵文字デコード
		return $str;
	}

	/**
	 * デコード
	 * 抽象絵文字コードからモバイルコードへ（キャリアは自動判定）
	 * 文字コードも変換
	 *
	 * @param	string	$str
	 * @return	string	$str
	 */
	function main_option($str)
	{
		$str = $this->_strCodeDecode($str);			// 文字列文字コードデコード
		$str = $this->_decode2($str);				// 絵文字デコード
		return $str;
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _decode()
	
	/**
	 * 絵文字デコード
	 *
	 * @param	string	$str			変換前	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 * @param  string  $option			1:タグも出力 0:モバイルコードのみ出力（docomoのみ対応）
	 * @return	string	$str			変換後	例）晴れ曇り
	 */
	function _decode($str, $option )
	{
		$left = preg_replace("/(\[|\])/", "\\\\$1", $this->_left_delimiter);
		$right = preg_replace("/(\[|\])/", "\\\\$1", $this->_right_delimiter);
		$pattarn = $left . "((?:d|s|e)-[\d\w]+)" . $right;
		$c['d'] =& new Emo_Code_docomo;
		$c['e'] =& new Emo_Code_EZweb;
		$c['s'] =& new Emo_Code_SoftBank;
		if (preg_match_all("/{$pattarn}/", $str, $matches)) {
			foreach ($matches[1] as $key => $ecode) {
				$sp = $this->_utility->splitEcode($ecode);
				// 正しい絵文字コードの時
				if ($c[$sp['carrier']]->isCode($sp['code'])) {
					$str = str_replace($matches[0][$key], $this->_decodeDirect($ecode, null), $str, $option);
				}
			}
		}
		return $str;
	}

	/**
	 * 絵文字デコード
	 *
	 * @param	string	$str			変換前	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 * @return	string	$str			変換後	例）晴れ曇り
	 */
	function _decode2($str)
	{
		$left = preg_replace("/(\[|\])/", "\\\\$1", $this->_left_delimiter);
		$right = preg_replace("/(\[|\])/", "\\\\$1", $this->_right_delimiter);
		$pattarn = $left . "((?:d|s|e)-[\d\w]+)" . $right;
		$c['d'] =& new Emo_Code_docomo;
		$c['e'] =& new Emo_Code_EZweb;
		$c['s'] =& new Emo_Code_SoftBank;
		if (preg_match_all("/{$pattarn}/", $str, $matches)) {
			foreach ($matches[1] as $key => $ecode) {
				$sp = $this->_utility->splitEcode($ecode);
				// 正しい絵文字コードの時
				if ($c[$sp['carrier']]->isCode($sp['code'])) {
					$str = str_replace($matches[0][$key], $this->_decodeDirect2($ecode), $str);
				}
			}
		}
		return $str;
	}
	
	// }}}
	// {{{ _strCodeDecode()
	
	/**
	 * 文字列文字コードデコード
	 *
	 * @param	string	$str
	 * @return	string	$str
	 */
	function _strCodeDecode($str)
	{
		$cname = $this->_utility->carrierShortToLongOtherPC($this->_carrier_v);	// キャリア名取得[Voda3G含む]
		$to = $this->_code_type["out_" . $cname];
		$from = $this->_code_type["set_base"];	// 読み込み元
		if ($to) {
			// [[Emo.type_out]]置換
			$rep = $this->_left_delimiter . "Emo.type_out" . $this->_right_delimiter;
			if (preg_match("/" . $rep . "/", $str, $matches)) {
				$str = str_replace($rep, $to, $str);
			}
			$str = $this->_utility->mbConvertEncoding($str, $to, $from);
			
			// エンコード自動認識hidden付加
			$encode = $this->_utility->mbConvertEncoding($this->_auto_encode_data['value'], $to, $this->_type_script);
			$str = preg_replace("!(</form>)!i", "<input type='hidden' name='" . $this->_auto_encode_data['name'] ."' value='" . $encode . "'>\n$1", $str);
		}
		return $str;
	}
	
	// }}}
	// {{{ _decodeDirect()
	
	/**
	 * デコード(コード直接指定)
	 *
	 * @param	string	$ecode			例）e-E63E
	 * @param  string  $option			1:タグも出力 0:モバイルコードのみ出力（docomoのみ対応）
	 * @return	string	$rtn			モバイルコード
	 */
	function _decodeDirect($ecode, $option)
	{
		$cname_v = $this->_utility->carrierShortToLongOtherPC($this->_carrier_v);	// キャリア名取得[Voda3G含む]
		$to = $this->_code_type["out_" . $cname_v];
		switch ($this->_carrier) {
		// モバイル
		case "d":
			$cname = $this->_utility->carrierShortToLong($this->_carrier);
			$class_name = "Emo_Code_" . $cname;
			$c =& new $class_name;
			$conv =& new Emo_Convert;
			$rtn = $conv->modifiy($ecode, $this->_carrier);
			// キャリア間絵文字相互変換後、対応した絵文字が有った場合
			if (preg_match("/^[\w\d\|]+$/", $rtn)) {	// ecodeのみ
				$rtn = $this->_toMcodeDocomo($rtn, $c, $cname_v, $option);
			// キャリア間絵文字相互変換後、対応した絵文字が無かった場合
			} else {
				$rtn = $this->_utility->mbConvertEncoding($rtn, $to, $this->_type_script);	// 文字コード変換
			}
			break;
		case "e":
		case "s":
			$cname = $this->_utility->carrierShortToLong($this->_carrier);
			$class_name = "Emo_Code_" . $cname;
			$c =& new $class_name;
			$conv =& new Emo_Convert;
			$rtn = $conv->modifiy($ecode, $this->_carrier);
			// キャリア間絵文字相互変換後、対応した絵文字が有った場合
			if (preg_match("/^[\w\d\|]+$/", $rtn)) {	// ecodeのみ
				$rtn = $this->_toMcode($rtn, $c, $cname_v);
			// キャリア間絵文字相互変換後、対応した絵文字が無かった場合
			} else {
				$rtn = $this->_utility->mbConvertEncoding($rtn, $to, $this->_type_script);	// 文字コード変換
			}
			break;
		// pc
		default:
			// 画像表示
			$sp = $this->_utility->splitEcode($ecode);
			$rtn = $this->_toImg($sp['carrier'], $sp['code']);
			$rtn = $this->_utility->mbConvertEncoding($rtn, $to, $this->_type_script);	// 文字コード変換
			break;
		}
		return $rtn;
	}

	/**
	 * デコード(コード直接指定)
	 *
	 * @param	string	$ecode			例）e-E63E
	 * @return	string	$rtn			モバイルコード
	 */
	function _decodeDirect2($ecode)
	{
		$cname_v = $this->_utility->carrierShortToLongOtherPC($this->_carrier_v);	// キャリア名取得[Voda3G含む]
		$to = $this->_code_type["out_" . $cname_v];
		switch ($this->_carrier) {
		// モバイル
		case "d":
		case "e":
		case "s":
			$cname = $this->_utility->carrierShortToLong($this->_carrier);
			$class_name = "Emo_Code_" . $cname;
			$c =& new $class_name;
			$conv =& new Emo_Convert;
			$rtn = $conv->modifiy($ecode, $this->_carrier);
			// キャリア間絵文字相互変換後、対応した絵文字が有った場合
			if (preg_match("/^[\w\d\|]+$/", $rtn)) {	// ecodeのみ
				//$rtn = $this->_toMcode2($rtn, $c, $cname_v);
				$rtn = $this->_toMcode2($rtn, $c);
			// キャリア間絵文字相互変換後、対応した絵文字が無かった場合
			} else {
				$rtn = $this->_utility->mbConvertEncoding($rtn, $to, $this->_type_script);	// 文字コード変換
			}
			break;
		// pc
		default:
			// 画像表示
			$sp = $this->_utility->splitEcode($ecode);
			$rtn = $this->_toImg($sp['carrier'], $sp['code']);
			$rtn = $this->_utility->mbConvertEncoding($rtn, $to, $this->_type_script);	// 文字コード変換
			break;
		}
		return $rtn;
	}
	
	// }}}
	// {{{ _toImg()
	
	/**
	 * 画像に変換
	 *
	 * @param	string	$carrier	キャリア名						例）d, e, s
	 * @param	string	$code		キャリア名除いた絵文字コード	例）E63E
	 * @return	string	$rtn		HTML画像タグ					例）<img src='～' >
	 */
	function _toImg($carrier, $code)
	{
		// EZwebのとき
		if ($carrier == "e") {
			$c =& new Emo_Code_EZweb;
			$all = $c->getAll();
			// 絵文字画像が無い場合(ブランク絵文字の場合)
			if ($all[$code]['img'] != 1) {
				$noimg = 1;
			}
		}
		// 絵文字名取得
		$cname = $this->_utility->carrierShortToLong($carrier);
		$class_name = "Emo_Code_" . $cname;
		$c =& new $class_name;
		$name = $c->getName($code);
		if (!isset($noimg)) {
			$rtn = "<img src='" . $this->_img_dir . "/{$carrier}/{$code}.gif' alt='" . $name . "' border='0' />";
		}
		return $rtn;
	}
	
	// }}}
	// {{{ _toMcode()
	
	/**
	 * モバイルコードに変換
	 *
	 * @param	string	$code	例）E63E
	 * @param	object	$c		絵文字コード集クラスインスタンス
	 * @param	string	$cname	キャリア長名	例）docomo
	 * @return	string	$rtn	モバイルコード
	 */
	function _toMcode($code, $c, $cname)
	{
		$type = $this->_code_type["get_" . $cname];
		// 絵文字コードが２つ連なっている時
		if (preg_match("/\|/", $code)) {
			$ex = explode("|", $code);
			foreach ($ex as $key => $value) {
				$rtn .= $c->toMcode($value, $type);
			}
		// 絵文字コードが１つの時
		} else {
			$rtn = $c->toMcode($code, $type);
		}
		return $rtn;
	}

	/**
	 * モバイルコードに変換
	 *
	 * @param	string	$code	例）E63E
	 * @param	object	$c		絵文字コード集クラスインスタンス
	 * @param	string	$cname	キャリア長名	例）docomo
	 * @return	string	$rtn	モバイルコード
	 */
	function _toMcode2($code, $c, $type = "utf-8")
	{
		//$type = $this->_code_type["get_" . $cname];
		//$type = "utf-8";
		// 絵文字コードが２つ連なっている時
		if (preg_match("/\|/", $code)) {
			$ex = explode("|", $code);
			foreach ($ex as $key => $value) {
				$rtn .= $c->toMcode($value, $type);
			}
		// 絵文字コードが１つの時
		} else {
			$rtn = $c->toMcode($code, $type);
		}
		return $rtn;
	}
	
	/**
	 * モバイルコードに変換(ドコモ用）
	 *
	 * @param	string	$code	例）E63E
	 * @param	object	$c		絵文字コード集クラスインスタンス
	 * @param	string	$cname	キャリア長名	例）docomo
	 * @param  string  $option	1:タグも出力 0:モバイルコードのみ出力（docomoのみ対応）
	 * @return	string	$rtn	モバイルコード
	 */
	function _toMcodeDocomo($code, $c, $cname, $option )
	{
		$type = $this->_code_type["get_" . $cname];
		// 絵文字コードが２つ連なっている時
		if (preg_match("/\|/", $code)) {
			$ex = explode("|", $code);
			foreach ($ex as $key => $value) {
				$rtn .= $c->toMcode($value, $type, $option);
			}
		// 絵文字コードが１つの時
		} else {
			$rtn = $c->toMcode($code, $type, $option);
		}
		return $rtn;
	}
	/**#@-*/
	
	// }}}
}

// }}}

?>