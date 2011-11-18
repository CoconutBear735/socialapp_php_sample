<?php

// {{{ Emo_Encode_SoftBank

/**
 * エンコードSoftBank
 */
class Emo_Encode_SoftBank extends Emo_Encode_Core
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_left_delimiter;
	var $_right_delimiter;
	var $_code_type;
	var $_code;
	var $_carrier_v;
	var $_cname;
	
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
	function Emo_Encode_SoftBank(&$context)
	{
		$this->_left_delimiter = $context->get("left_delimiter");
		$this->_right_delimiter = $context->get("right_delimiter");
		$this->_code_type = $context->get("code_type");
		$this->_carrier_v = $context->get("carrier_v");		// キャリア名取得[Voda3G含む]
	}
	
	// }}}
	// {{{ main()
	
	/**
	 * SoftBank変換
	 *
	 * @param	string	$str	変換前	例）晴れ$Gj曇り$Gi
	 * @return	string	$str	変換後	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 */
	function main($str)
	{
		if (!$this->_cname) {
			$this->_cname = Emo_Utility::carrierShortToLong($this->_carrier_v);	// キャリア名取得[Voda3G含む]
		}
		
		if (!$this->_code) {
			$this->_code =& new Emo_Code_SoftBank;
		}
		Log::DebugOut("main type=".$this->_code_type["set_" . $this->_cname]);
		// 実行
//		if ($this->_code_type["set_" . $this->_cname] == "web") {
//			return $this->_doWeb($str);
//		} else if ($this->_code_type["set_" . $this->_cname] == "sjis") {
//			return $this->_doSJIS($str);
//		} else if ($this->_code_type["set_" . $this->_cname] == "utf-8") {
//			return $this->_doUTF8($str);
//		} else {
			return $this->_doUTF8($str);
//		}
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _doSJIS()
	
	/**
	 * 実行（sjis）
	 *
	 * @param	string	$str
	 * @return	string	$rtn
	 */
	function _doSJIS($str)
	{
		$emoji = '[\xF7\xF9\xFB][\x41-\x7E\x80-\x9B\xA1-\xFA]';		// sjis	自作正規表現、間違いある？
		$arr = $this->splitOneSJIS($str);	// １文字単位に分割
		$rtn = "";
		foreach ($arr as $value) {
			$rtn .= $this->_exec($value, $emoji);
		}
		return $rtn;
	}
	
	// }}}
	// {{{ _doUTF8()
	
	/**
	 * 実行（utf-8）
	 *
	 * @param	string	$str
	 * @return	string	$this->_exec($str, $emoji)
	 */
	function _doUTF8($str)
	{
		$emoji = '\xEE[\x80\x81\x84\x85\x88\x89\x8C\x8D\x90\x91\x94][\x80-\xBF]';	// utf-8
		return $this->_exec($str, $emoji);
	}
	
	// }}}
	// {{{ _doWeb()
	
	/**
	 * 実行（web）
	 *
	 * @param	string	$str
	 * @return	string	$this->_exec($str, $emoji)
	 */
	function _doWeb($str)
	{
		$emoji = '([\x1B][\x24][GEFOPQ])([\x21-\x7E]+)([\x0F]|$)';		// webコード	// 古い機種の一部で最終行が絵文字の場合[\x0F]が無い場合がある
		return $this->_exec($str, $emoji);
	}
	
	// }}}
	// {{{ _exec()
	
	/**
     * エンコード実行
	 *
	 * @param	string	$str
	 * @param	string	$emoji	絵文字正規表現
	 * @return	string	$str
	 */
	function _exec($str, $emoji)
	{
		if (preg_match_all("/(?:$emoji)/", $str, $matches)) {
			// webコード以外の時
			if ($this->_code_type["set_" . $this->_cname] != "web") {
				$codes = $matches[0];
			// webコードの時
			} else {
				// 連結文字列を展開
				foreach ($matches[0] as $key => $value) {
					if (preg_match_all("/[\x21-\x7E]{1}/", $matches[2][$key], $ones)) {
						$tenkai = "";
						foreach ($ones[0] as $one) {
							$code = $matches[1][$key] . $one . "\x0F";
							$tenkai .= $code;
							$codes[] = $code;
						}
						// 展開絵文字に置換
						$str = str_replace($matches[0][$key], $tenkai, $str);
					}
				}
			}
			// 置換
			foreach ($codes as $key => $mcode) {
				$code = $this->_code->toKey($mcode, $this->_code_type["set_" . $this->_cname]);
				$str = str_replace($codes[$key], $this->_left_delimiter . "s-" . $code . $this->_right_delimiter, $str);
			}
		}
		return $str;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>