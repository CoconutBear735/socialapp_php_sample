<?php
require_once(dirname(__FILE__) . '/../module/Net/UserAgent/Mobile.php');

// {{{ Emo_UserAgent

/**
 * ユーザエイジェントクラス
 */
class Emo_UserAgent
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	/**
	 * ユーザエイジェント
	 */
	var $_agent_string;
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 */
	function Emo_UserAgent($agent_string = "")
	{
		if (!$agent_string) {
			$agent_string = $_SERVER['HTTP_USER_AGENT'];
		}
		$this->_agent_string = $agent_string;
	}
	
	// }}}
	// {{{ getCarrier()
	
	/**
	 * キャリア名取得
	 *
	 * @return	string	キャリア名
	 */
	function getCarrier()
	{
		$agent =& Net_UserAgent_Mobile::singleton($this->_agent_string);
		if (Net_UserAgent_Mobile::isError($agent)) {
			return false;
		}
		if ($agent->isDoCoMo()) {
			return "docomo";
		} else if($agent->isEZweb()) {
			return "EZweb";
		} else if($agent->isSoftBank()) {
			return "SoftBank";
		}
	}
	
	// }}}
	// {{{ getCarrierShortName()
	
	/**
	 * 短いキャリア名取得
	 *
	 * @return	string	キャリア名	例）d, e, s
	 */
	function getCarrierShortName()
	{
		$agent =& Net_UserAgent_Mobile::singleton($this->_agent_string);
		if (Net_UserAgent_Mobile::isError($agent)) {
			return false;
		}
		if ($agent->isDoCoMo()) {
			return "d";
		} else if($agent->isEZweb()) {
			return "e";
		} else if($agent->isSoftBank()) {
			return "s";
		}
	}
	
	// }}}
	// {{{ isMobile()
	
	/**
	 * モバイル判定
	 *
	 * @return	boolean		モバイルフラグ
	 */
	function isMobile()
	{
		$agent =& Net_UserAgent_Mobile::singleton($this->_agent_string);
		if (Net_UserAgent_Mobile::isError($agent)) {
			return false;
		}
		if ($agent->isDoCoMo()) {
			return true;
		} elseif ($agent->isEZweb()) {
			return true;
		} elseif ($agent->isSoftBank()) {
			return true;
		}
		return false;
	}
	
	// }}}
	// {{{ isVoda3G()
	
	/**
	 * Vodafone(3G判定)
	 */
	function isVoda3G()
	{
		$agent =& Net_UserAgent_Mobile::singleton($this->_agent_string);
		if (Net_UserAgent_Mobile::isError($agent)) {
			return false;
		}
		if ($agent->isSoftbank()) {
			$exp = explode(' ', $this->_agent_string);
			preg_match('!^(?:(Vodafone|Vemulator)/\d\.\d|MOT-|MOTEMULATOR)!', $exp[0], $matches);
			if (count($matches) >= 1) {
				return true;
			}
		}
		return false;
	}
	
	// }}}
	// {{{ getCarrierShortNameAddVoda3G()
	
	/**
	 * 短いキャリア名取得(Voda3G含む)
	 *
	 * @return	string	キャリア名	例）d, e, s, v3
	 */
	function getCarrierShortNameAddVoda3G()
	{
		if ($this->isVoda3G()) {
			return "v3";
		} else {
			return $this->getCarrierShortName();
		}
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>