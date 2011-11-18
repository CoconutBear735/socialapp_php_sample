<?php

// {{{ Emo_Utility

/**
 * 絵文字ユーティリティクラス
 */
class Emo_Utility
{
	/**#@+
	 * @access public
	 */
	
	// {{{ splitEcode()
	
	/**
	 * キャリアと絵文字コード分割
	 *
	 * @param	string	$ecode	絵文字コード		例）d-E63F
	 * @return	array	$rtn	分割絵文字コード	例）array("carrier" => "d", "code" => "E63F")
	 */
	function splitEcode($ecode)
	{
		list($rtn['carrier'], $rtn['code']) = split("-", $ecode);
		return $rtn;
	}
	
	// }}}
	// {{{ carrierShortToLong()
	
	/**
	 * キャリア短名→キャリア長名
	 *
	 * @param	string	$name	キャリア短名
	 * @return	string	$name	キャリア長名
	 */
	function carrierShortToLong($name)
	{
		switch ($name) {
		case "d":
			$name = "docomo";
			break;
		case "e":
			$name = "EZweb";
			break;
		case "s":
			$name = "SoftBank";
			break;
		case "v3":
			$name = "Voda3G";
			break;
		}
		return $name;
	}
	
	/**
	 * キャリア短名→キャリア長名(該当無し時→pc)
	 *
	 * @param	string	$name	キャリア短名
	 * @return	string	$name	キャリア長名
	 */
	function carrierShortToLongOtherPC($name)
	{
		$name = $this->carrierShortToLong($name);
		if (!$name) {
			$name = "pc";
		}
		return $name;
	}
	
	// }}}
	// {{{ mbConvertEncoding()
	
	/**
	 * 文字コード変換
	 *
	 * phpのmb_convert_encodingと同じで、別の文字コードに変換する時のみ処理
	 * @param	string	$str
	 * @param	string	$to		変換後文字コード	例）sjis, utf-8
	 * @param	string	$from	変換前文字コード	例）sjis, utf-8
	 * @return	string	$str
	 */
	function mbConvertEncoding($str, $to, $from)
	{
		if ($to && $from && $to != $from) {
			return mb_convert_encoding($str, $to, $from);
		}
		return $str;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>