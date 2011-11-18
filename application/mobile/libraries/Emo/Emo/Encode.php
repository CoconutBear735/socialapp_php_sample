<?php
require_once(dirname(__FILE__) . "/Encode/Core.php");
require_once(dirname(__FILE__) . "/Encode/docomo.php");
require_once(dirname(__FILE__) . "/Encode/EZweb.php");
require_once(dirname(__FILE__) . "/Encode/SoftBank.php");

// {{{ Emo_Encode

/**
 * エンコード
 * モバイルコードから抽象絵文字コードへ
 * 文字コードも変換
 */
class Emo_Encode
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_context;
	var $_carrier;
	var $_carrier_v;
	var $_code_type;
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
	function Emo_Encode(&$context)
	{
		$this->_carrier = $context->get("carrier");			// キャリア名取得
		$this->_carrier_v = $context->get("carrier_v");
		$this->_code_type = $context->get("code_type");
		$this->_context = $context;
		$this->_utility =& new Emo_Utility;
	}
	
	// }}}
	// {{{ main()
	
	/**
	 * エンコード
	 * モバイルコードから抽象絵文字コードへ
	 * 文字コードも変換
	 *
	 * @param	string	$str			変換前	例）晴れ曇り
	 * @return	string	$str			変換後	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 */
	function main($str)
	{
		$str = $this->_encode($str);			// 絵文字エンコード
		$str = $this->_strCodeEncode($str);		// 文字列文字コードエンコード
		return $str;
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _encode()
	
	/**
	 * 絵文字エンコード
	 *
	 * @param	string	$str
	 * @return	string	$str
	 */
	function _encode($str)
	{
		switch ($this->_carrier) {
		case "d":
			$c =& new Emo_Encode_docomo($this->_context);
			$str = $c->main($str);
			break;
		case "e":
			$c =& new Emo_Encode_EZweb($this->_context);
			$str = $c->main($str);
			break;
		case "s":
			$c =& new Emo_Encode_SoftBank($this->_context);
			$str = $c->main($str);
			break;
		}
		return $str;
	}
	
	// }}}
	// {{{ _strCodeEncode()
	
	/**
	 * 文字列文字コードエンコード
	 *
	 * @param	string	$str
	 * @return	string	$str
	 */
	function _strCodeEncode($str) {
		$cname = $this->_utility->carrierShortToLongOtherPC($this->_carrier_v);	// キャリア名取得[Voda3G含む]
		$from = $this->_code_type["in_" . $cname];
		$to = $this->_code_type["get_base"];	// 読み込み元
		if ($to != $from && $from && $to) {
			$str = $this->_utility->mbConvertEncoding($str, $to, $from);		// エンコード
		}
		return $str;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>