<?php
require_once(dirname(__FILE__) . "/Convert/Core.php");
require_once(dirname(__FILE__) . "/Convert/docomo.php");
require_once(dirname(__FILE__) . "/Convert/EZweb.php");
require_once(dirname(__FILE__) . "/Convert/SoftBank.php");
require_once(dirname(__FILE__) . "/Utility.php");

// {{{ Emo_Convert

/**
 * 絵文字3キャリア相互コンバートクラス
 */
class Emo_Convert
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_utility;
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 */
	function Emo_Convert()
	{
		$this->_utility =& new Emo_Utility;
	}
	
	// }}}
	// {{{ modifiy()
	
	/**
	 * 絵文字コード変換
	 *
	 * @param	string	$ecode	絵文字コード		例）d-E63E等
	 * @param	string	$to		キャリア短名		例）d, e, s
	 * @return	string	$rtn	変換後絵文字コード	例）e-E481等
	 */
	function modifiy($ecode, $to)
	{
		$sp = $this->_utility->splitEcode($ecode);
		$from = $this->_utility->carrierShortToLong($sp['carrier']);
		$class = "Emo_Convert_" . $from;
		$c =& new $class;
		switch ($to) {
		case "d":
			$rtn = $c->docomo($sp['code']);
			break;
		case "e":
			$rtn = $c->EZweb($sp['code']);
			break;
		case "s":
			$rtn = $c->SoftBank($sp['code']);
			break;
		}
		return $rtn;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>