<?php

// {{{ Emo_Encode_EZweb

/**
 * エンコードEZweb
 */
class Emo_Encode_EZweb extends Emo_Encode_Core
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_left_delimiter;
	var $_right_delimiter;
	var $_code_type;
	var $_code;
	
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
	function Emo_Encode_EZweb(&$context)
	{
		$this->_left_delimiter = $context->get("left_delimiter");
		$this->_right_delimiter = $context->get("right_delimiter");
		$this->_code_type = $context->get("code_type");
	}
	
	// }}}
	// {{{ main()
	
	/**
	 * EZweb変換
	 *
	 * @param	string	$str	変換前	例）晴れ曇り
	 * @return	string	$str	変換後	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 */
	function main($str)
	{
		if (!$this->_code) {
			$this->_code =& new Emo_Code_EZweb;
		}
		// 実行
		if ($this->_code_type["set_EZweb"] == "sjis") {
			return $this->_doSJIS($str);
		} else if ($this->_code_type["set_EZweb"] == "utf-8") {
			return $this->_doUTF8($str);
		} else if ($this->_code_type["set_EZweb"] == "tag") {
			return $this->_doTag($str);
		}
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
		$emoji = '[\xF3\xF4\xF6\xF7][\x40-\x7E\x80-\xFC]';	// sjis
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
		$emoji = '(?:\xEE[\xB1-\xB3\xB5\xB6\xBD-\xBF]|\xEF[\x81-\x83])[\x80-\xBF]';	// utf-8	自作正規表現、間違いある？
		return $this->_exec($str, $emoji);
	}
	
	// }}}
	// {{{ _doTag()
	
	/**
	 * 実行（tag）
	 *
	 * @param	string	$str
	 * @return	string	$this->_exec($str, $emoji)
	 */
	function _doTag($str)
	{
		$emoji = '<img localsrc=[\"\']?[\d]+[\"\']?>';	// tag
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
			// 置換
			foreach ($matches[0] as $key => $mcode) {
				$code = $this->_code->toKey($mcode, $this->_code_type["set_EZweb"]);
				$str = str_replace($matches[0][$key], $this->_left_delimiter . "e-" . $code . $this->_right_delimiter, $str);
			}
		}
		return $str;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>