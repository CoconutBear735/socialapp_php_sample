<?php

// {{{ Emo_Convert_Core

/**
 * 3キャリア絵文字コードの相互変換
 */
class Emo_Convert_Core
{
	// {{{ docomo()
	
	/**#@+
	 * @access public
	 */
	
	/**
	 * docomoコードへ変換
	 *
	 * @param	string	$code			絵文字コード
	 * @return	string	$hash['docomo']	変換後コード
	 */
	function docomo($code)
	{
		$hash = $this->_get($code);
		if (isset($hash['docomo'])) {
			return $hash['docomo'];
		} else {
			return $code;
		}
	}
	
	// }}}
	// {{{ EZweb()
	
	/**
	 * EZwebコードへ変換
	 *
	 * @param	string	$code			絵文字コード
	 * @return	string	$hash['EZweb']	変換後コード
	 */
	function EZweb($code)
	{
		$hash = $this->_get($code);
		if ($hash['EZweb']) {
			return $hash['EZweb'];
		} else {
			return $code;
		}
	}
	
	// }}}
	// {{{ SoftBank()
	
	/**
	 * SoftBankコードへ変換
	 *
	 * @param	string	$code				絵文字コード
	 * @return	string	$hash['SoftBank']	変換後コード
	 */
	function SoftBank($code)
	{
		$hash = $this->_get($code);
		if ($hash['SoftBank']) {
			return $hash['SoftBank'];
		} else {
			return $code;
		}
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _get()
	
	/**
	 * 特定のコードの変換レコードを取得
	 *
	 * @param	string	$code			絵文字コード
	 * @return	array	$conv[$code]	絵文字変換レコード
	 */
	function _get($code)
	{
		$conv = $this->getAll();
		return $conv[$code];
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>