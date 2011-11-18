<?php
require_once(dirname(__FILE__) . "/Data/SoftBank.php");

// {{{ Emo_Code_EZweb

/**
 * SoftBank絵文字コードクラス
 */
class Emo_Code_SoftBank extends Emo_Code_Data_SoftBank
{
	// {{{ toMcode()
	
	/**#@+
	 * @access public
	 */
	
	/**
	 * モバイル端末表示文字列に変換
	 *
	 * @param	string	$code	絵文字コード	例）E001
	 * @param	string	$type	文字コード
	 * @return	string			モバイルコード	例）$G!
	 */
	function toMcode($code, $type = "web")
	{
		if ($type == "web") {
			return $this->_toMcodeWeb($code);
		} else if ($type == "sjis") {
			return $this->_toMcodeSJIS($code);
		} else if ($type == "utf-8") {
			return $this->_toMcodeUTF8($code);
		} else if ($type == "unicode") {
			return "&#x" . $code;
		}
	}
	
	// }}}
	// {{{ toKey()
	
	/**
	 * モバイル端末表示文字列からキーに変換
	 *
	 * @param	string	$mcode	モバイルコード	例）$G!
	 * @param	string	$type	文字コード
	 * @return	string			絵文字コード	例）E001
	 */
	function toKey($mcode, $type = "web")
	{
		$all = $this->getAll();
		if ($type == "web") {
			return $this->_toKeyWeb($mcode, $all);
		} else if ($type == "sjis") {
			return $this->_toKeySJIS($mcode, $all);
		} else if ($type == "utf-8") {
			return $this->_toKeyUTF8($mcode, $all);
		} else {
			return $this->_toKeyUTF8($mcode, $all);
		}
	}
	
	// }}}
	// {{{ getWeb()
	
	/**
	 * 絵文字webコード取得
	 *
	 * @param	string	$code			絵文字コード		例）E001
	 * @return	string	$row['web']		絵文字ウェブコード	例）$G!
	 */
	function getWeb($code)
	{
		$row = $this->getRecode($code);
		return $row['web'];
	}
	
	// }}}
	// {{{ getDec()
	
	/**
	 * 絵文字10進コード取得
	 *
	 * @param	string	$code			絵文字コード		例）E001
	 * @return	string	$row['dec']		絵文字10進コード	例）&#57345;
	 */
	function getDec($code)
	{
		$row = $this->getRecode($code);
		return $row['dec'];
	}
	
	// }}}
	// {{{ getHex()
	
	/**
	 * 絵文字16進コード取得
	 *
	 * @param	string	$code			絵文字コード		例）E001
	 * @return	string	$row['hex']		絵文字10進コード	例）&#xE001;
	 */
	function getHex($code)
	{
		$row = $this->getRecode($code);
		return $row['hex'];
	}
	
	// }}}
	// {{{ getH()
	
	/**
	 * 絵文字ウェブコードヘッダ取得
	 *
	 * @param	string	$code			絵文字コード				例）E001
	 * @return	string	$row['hex']		絵文字ウェブコードヘッダ	例）G
	 */
	function getH($code)
	{
		$row = $this->getRecode($code);
		return $row['h'];
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _toMcodeWeb()
	
	/**
	 * モバイル端末表示文字列に変換(web)
	 *
	 * @param	string	$code	絵文字コード	例）E001
	 * @return	string	$mcode	モバイルコード	例）$G!
	 */
	function _toMcodeWeb($code)
	{
		$mcode = $this->getWeb($code);
		if ($mcode) {
			return $mcode;
		}
	}
	
	// }}}
	// {{{ _toMcodeSJIS()
	
	/**
	 * モバイル端末表示文字列に変換(sjis)
	 *
	 * @param	string	$code				絵文字コード	例）E001
	 * @return	string	pack("H*", $mcode)	モバイルコード	例）pack("H*", "F941")
	 */
	function _toMcodeSJIS($code)
	{
		$mcode = $this->getSJIS($code);
		if ($mcode) {
			return pack("H*", $mcode);
		}
	}
	
	// }}}
	// {{{ _toMcodeUTF8()
	
	/**
	 * モバイル端末表示文字列に変換(utf-8)
	 *
	 * @param	string	$code				絵文字コード	例）E001
	 * @return	string	pack("H*", $mcode)	モバイルコード	例）pack("H*", "EE8081")
	 */
	function _toMcodeUTF8($code)
	{
		$mcode = $this->getUTF8($code);
		if ($mcode) {
			return pack("H*", $mcode);
		}
	}
	
	// }}}
	// {{{ _toKeyWeb()
	
	/**
	 * モバイル端末表示文字列からキーに変換(web)
	 *
	 * @param	string	$mcode	モバイルコード	例）$G!
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E001
	 */
	function _toKeyWeb($mcode, $all)
	{
		foreach ($all as $key => $value) {
			if ($value['web'] == $mcode) {
				return $key;
			}
		}
	}
	
	// }}}
	// {{{ _toKeySJIS()
	
	/**
	 * モバイル端末表示文字列からキーに変換(sjis)
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "F941")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E001
	 */
	function _toKeySJIS($mcode, $all)
	{
		foreach ($all as $key => $value) {
			if (pack("H*", $value['sjis']) == $mcode) {
				return $key;
			}
		}
	}
	
	// }}}
	// {{{ _toKeyUTF8()
	
	/**
	 * モバイル端末表示文字列からキーに変換(utf-8)
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "EE8081")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E001
	 */
	function _toKeyUTF8($mcode, $all)
	{
		foreach ($all as $key => $value) {
			if (pack("H*", $value['utf-8']) == $mcode) {
				return $key;
			}
		}
	}

	/**
	 * モバイル端末表示文字列からキーに変換(utf-8)
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "EE8081")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E001
	 */
	function _toWeb_frUTF8($mcode)
	{
		$all = $this->getAll();

		foreach ($all as $key => $value) {
			if (pack("H*", $value['utf-8']) == $mcode) {
				return $value['web'];
			}
		}
	}	
	/**#@-*/
	
	// }}}
}

// }}}

?>
