<?php
require_once(dirname(__FILE__) . "/Data/docomo.php");

// {{{ Emo_Code_docomo

/**
 * docomo絵文字コードクラス
 */
class Emo_Code_docomo extends Emo_Code_Data_docomo
{
	/**#@+
	 * @access public
	 */
	
	// {{{ toMcode()
	
	/**
	 * モバイル端末表示文字列に変換
	 *
	 * @param	string	$code	絵文字コード	例）E63E
	 * @param	string	$type	文字コード
	 * @param  string  $option	1:タグも出力 0:モバイルコードのみ出力（docomoのみ対応）
	 * @return	string			モバイルコード	例）pack("H*", "F89F")
	 */
	function toMcode($code, $type = "sjis", $option = 1)
	{
		if ($type == "sjis") {
			return $this->_toMcodeSJIS($code, $option);
		} else if ($type == "utf-8") {
			return $this->_toMcodeUTF8($code, $option);
		}
	}
	
	// }}}
	// {{{ toKey()
	
	/**
	 * モバイル端末表示文字列からキーに変換
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "F89F")
	 * @param	string	$type	文字コード
	 * @return	string			絵文字コード	例）E63E
	 */
	function toKey($mcode, $type = "sjis")
	{
		$all = $this->getAll();
		if ($type == "sjis") {
			return $this->_toKeySJIS($mcode, $all);
		} else if ($type == "utf-8") {
			return $this->_toKeyUTF8($mcode, $all);
		}
	}
	
	// }}}
	// {{{ getSJIST()
	
	/**
	 * 絵文字sjisテキスト取得
	 *
	 * @param	string	$code			絵文字コード		例）E63E
	 * @return	string	$row['sjis_t']	sjis_tテキスト		例）&#63647;
	 */
	function getSJIST($code)
	{
		$row = $this->getRecode($code);
		return $row['sjis_t'];
	}
	
	// }}}
	// {{{ getColor()
	
	/**
	 * 絵文字色コード取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	string	$row['color']	色コード		例）FF0000
	 */
	function getColor($code)
	{
		$row = $this->getRecode($code);
		return $row['color'];
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _toMcodeSJIS()
	
	/**
	 * モバイル端末表示文字列に変換(sjis)
	 *
	 * @param	string	$code		絵文字コード		例）E63E
	 * @return	string	$rtn		モバイルコード+タグ	例）<font color='#FF0000'>pack("H*", "F89F")</font>
	 */
	function _toMcodeSJIS($code, $option)
	{
		$mcode = $this->getSJIS($code);
		if ($mcode) {
			$bi = pack("H*", $mcode);
			$color = $this->getColor($code);
			if( $option == 0 ){
				$rtn = $bi;
			}
			else{
				$rtn = "<font color='#" . $color . "'>" . $bi . "</font>";
			}
			return $rtn;
		}
	}
	
	// }}}
	// {{{ _toMcodeUTF8()
	
	/**
	 * モバイル端末表示文字列に変換(utf-8)
	 *
	 * @param	string	$code		絵文字コード		例）E63E
	 * @return	string	$rtn		モバイルコード+タグ	例）<font color='#FF0000'>pack("H*", "EE98BE")</font>
	 */
	function _toMcodeUTF8($code, $option)
	{
		$mcode = $this->getUTF8($code);
		if ($mcode) {
			$bi = pack("H*", $mcode);
			$color = $this->getColor($code);
			if( $option == 0 ){
				$rtn = $bi;
			}
			else{
				$rtn = "<font color='#" . $color . "'>" . $bi . "</font>";
			}
			return $rtn;
		}
	}
	
	// }}}
	// {{{ _toKeySJIS()
	
	/**
	 * モバイル端末表示文字列からキーに変換(sjis)
	 *
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "F89F")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E63E
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
	 * @param	string	$mcode	モバイルコード	例）pack("H*", "EE98BE")
	 * @param	array	$all	絵文字レコード集
	 * @return	string	$key	絵文字コード	例）E63E
	 */
	function _toKeyUTF8($mcode, $all)
	{
		foreach ($all as $key => $value) {
			if (pack("H*", $value['utf-8']) == $mcode) {
				return $key;
			}
		}
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>