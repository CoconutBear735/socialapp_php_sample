<?php

// {{{ Emo_Encode_Core

/**
 * エンコードCore
 */
class Emo_Encode_Core
{
	/**#@+
	 * @access public
	 */
	
	// {{{ splitOneSJIS()
	
	/**
	 * １文字単位に分割して取得（sjis）
	 *
	 * @param	string	$str
	 * @return	array	$matches[0]
	 */
	function splitOneSJIS($str)
	{
		$char = '/(?:[\x00-\x7F\xA1-\xDF]|[\x81-\x9F\xE0-\xFC][\x40-\x7E\x80-\xFC])/';
		if (preg_match_all($char, $str, $matches)) {
			return $matches[0];
		}
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>