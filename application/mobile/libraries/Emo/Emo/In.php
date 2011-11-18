<?php

// {{{ Emo_In

/**
 * インデータクラス
 */
class Emo_In
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_context;
	var $_code_type;
	var $_auto_encode;
	var $_auto_encode_data;
	var $_type_script;
	var $_carrier_v;
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
	function Emo_In(&$context)
	{
		$this->_context = $context;
		$this->_code_type = $context->get("code_type");
		$this->_auto_encode = $context->get("auto_encode");
		$this->_auto_encode_data = $context->get("auto_encode_data");
		$this->_type_script = $context->get("type_script");
		$this->_carrier_v = $context->get("carrier_v");
		$this->_utility =& new Emo_Utility;
	}
	
	// }}}
	// {{{ requeste()
	
	/**
	 * requestエンコードデータ取得
	 *
	 * @return	string	$this->_encode($_REQUEST)
	 */
	function requeste()
	{
		return $this->_encode($_REQUEST);
	}
	
	// }}}
	// {{{ gete()
	
	/**
	 * getエンコードデータ取得
	 *
	 * @return	string	$this->_encode($_GET)
	 */
	function gete()
	{
		return $this->_encode($_GET);
	}
	
	// }}}
	// {{{ poste()
	
	/**
	 * postエンコードデータ取得
	 *
	 * @return	string	$this->_encode($_POST)
	 */
	function poste()
	{
		return $this->_encode($_POST);
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _encode()
	
	/**
	 * エンコード
	 *
	 * @param	array	$arr
	 * @return	array	$arr
	 */
	function _encode($arr) {
		if ($this->_auto_encode) {
			$this->_setAutoCodeEncode($arr);
		}
		$c =& new Emo_Encode($this->_context);
		foreach ($arr as $key => $value) {
			$arr[$key] = $c->main($value);			// エンコード
		}
		unset($arr[$this->_auto_encode_data['name']]);	// 文字コード自動エンコードのための付加データの除去
		return $arr;
	}
	
	// }}}
	// {{{ _setAutoCodeEncode()
	
	/**
	 * 自動文字コードエンコード
	 * $code_typeを上書きする
	 *
	 * @param	array	$arr	受信データ
	 * @return	void
	 */
	function _setAutoCodeEncode($arr) {
		if ($this->_auto_encode_data['name'] && $arr[$this->_auto_encode_data['name']] && $this->_auto_encode_data['value']) {
			switch ($arr[$this->_auto_encode_data['name']]) {
			case $this->_utility->mbConvertEncoding($this->_auto_encode_data['value'], "sjis", $this->_type_script):
				$from = "sjis";
				break;
			case $this->_utility->mbConvertEncoding($this->_auto_encode_data['value'], "utf-8", $this->_type_script):
				$from = "utf-8";
				break;
			case $this->_utility->mbConvertEncoding($this->_auto_encode_data['value'], "euc-jp", $this->_type_script):
				$from = "euc-jp";
				break;
			}
		}
		$cname = $this->_utility->carrierShortToLongOtherPC($this->_carrier_v);
		$this->_code_type["in_" . $cname] = $from;	// 文字列の文字コード
		if ($this->_code_type["set_" . $cname] != "web" && $this->_code_type["set_" . $cname] != "tag") {
			$this->_code_type["set_" . $cname] = $from;	// 絵文字の文字コード
		}
		$this->_context->set("code_type", $this->_code_type);	// 自動検出したエンコード文字コードをコンテキストに設定
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>