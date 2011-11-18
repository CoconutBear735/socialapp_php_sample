<?php
require_once(dirname(__FILE__) . "/Data/EZweb.php");

// {{{ Emo_Code_EZweb

/**
 * EZweb絵文字コードクラス
 */
class Emo_Code_EZweb extends Emo_Code_Data_EZweb
{
	// {{{ toMcode()
	
	/**#@+
	 * @access public
	 */
	
	/**
	 * モバイル端末表示文字列に変換
	 *
	 * @param	string	$code	絵文字コード	例）E481
	 * @param	string	$type	文字コード
	 * @return	string			モバイルコード	例）<img localsrc="1">
	 */
	function toMcode($code, $type = "tag")
	{
		if ($type == "tag") {
			return $this->_toMcodeTag($code);
		} else if ($type == "sjis") {
			return $this->_toMcodeSJIS($code);
		} else if ($type == "utf-8") {
			return $this->_toMcodeUTF8($code);
		}
	}
	
	// }}}
	// {{{ toKey()
	
	/**
	 * モバイル端末表示文字列からキーに変換
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "F659")
	 * @param	string	$type	文字コード
	 * @return	string			絵文字コード	例）E481
	 */
	function toKey($mcode, $type = "sjis")
	{
		$all = $this->getAll();
		if ($type == "sjis") {
			return $this->_toKeySJIS($mcode, $all);
		} else if ($type == "utf-8") {
			return $this->_toKeyUTF8($mcode, $all);
		} else if ($type == "tag") {
			return $this->_toKeyTag($mcode, $all);
		}
	}
	
	// }}}
	// {{{ getUni2()
	
	/**
	 * utf-8のページから絵文字をPOSTした場合のコード
	 *
	 * @link	http://mobilehacker.g.hatena.ne.jp/tomi-ru/20071112/1194857099
	 * @param	string	$code			絵文字コード	例）E481
	 * @return	string	$row['uni2']	絵文字utf-8		例）EF59
	 */
	function getUni2($code)
	{
		$row = $this->getRecode($code);
		return $row['uni2'];
	}
	
	// }}}
	// {{{ getMJIS()
	
	/**
	 * 絵文字メール用jis取得
	 *
	 * @param	string	$code			絵文字コード		例）E481
	 * @return	string	$row['m_jis']	絵文字メール用jis	例）753A
	 */
	function getMJIS($code)
	{
		$row = $this->getRecode($code);
		return $row['m_jis'];
	}
	
	// }}}
	// {{{ getMSJIS()
	
	/**
	 * 絵文字メール用sjis取得
	 *
	 * @param	string	$code			絵文字コード		例）E481
	 * @return	string	$row['m_sjis']	絵文字メール用sjis	例）EB59
	 */
	function getMSJIS($code)
	{
		$row = $this->getRecode($code);
		return $row['m_sjis'];
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _toMcodeTag()
	
	/**
	 * モバイル端末表示文字列に変換(tag)
	 *
	 * @param	string	$code	絵文字コード	例）E481
	 * @return	string			モバイルコード	例）<img localsrc="1">
	 */
	function _toMcodeTag($code)
	{
		$mcode = $this->getNo($code);
		if ($mcode) {
			return "<img localsrc=\"" . $mcode . "\">";
		}
	}
	
	// }}}
	// {{{ _toMcodeSJIS()
	
	/**
	 * モバイル端末表示文字列に変換(sjis)
	 *
	 * @param	string	$code				絵文字コード	例）E481
	 * @return	string	pack("H*", $mcode)	モバイルコード	例）pack("H*", "F659")
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
	 * @param	string	$code				絵文字コード	例）E481
	 * @return	string	pack("H*", $mcode)	モバイルコード	例）pack("H*", "EEBD99")
	 */
	function _toMcodeUTF8($code)
	{
		$mcode = $this->getUTF8($code);
		if ($this->getUTF8($code)) {
			return pack("H*", $mcode);
		}
	}
	
	// }}}
	// {{{ _toKeySJIS()
	
	/**
	 * モバイル端末表示文字列からキーに変換(sjis)
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "F659")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E481
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
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "EEBD99")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E481
	 */
	function _toKeyUTF8($mcode, $all)
	{
		foreach ($all as $key => $value) {
			if (pack("H*", $value['utf-8']) == $mcode) {
				return $key;
			}
		}
	}
	
	// }}}
	// {{{ _toKeyTag()
	
	/**
	 * モバイル端末表示文字列からキーに変換(tag)
	 *
	 * @param	string	$mcode	モバイルコード	例）<img localsrc="1">
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E481
	 */
	function _toKeyTag($mcode, $all)
	{
		if (preg_match("/[\d]+/", $mcode, $matches)) {
			$no = $matches[0];
			foreach ($all as $key => $value) {
				if ($value['no'] == $no) {
					return $key;
				}
			}
		}
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>
