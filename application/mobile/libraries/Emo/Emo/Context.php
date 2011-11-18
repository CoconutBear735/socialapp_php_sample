<?php

// {{{ Emo_Context

/**
 * コンテキストクラス
 */
class Emo_Context
{
	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	/**
	 * パラメータ
	 */
	var $_params = array();
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 *
	 * @param	object	プロパティ
	 */
	function Emo_Context($obj = "")
	{
		if ($obj) {
			foreach ($obj as $key => $value) {
				if (preg_match("/^type_([\w_]+)$/", $key, $matches)) {
					$this->_params['code_type'][$matches[1]] = $value;
				}
			}
			$this->_params['img_dir'] = $obj->img_dir;
			$this->_params['left_delimiter'] = $obj->left_delimiter;
			$this->_params['right_delimiter'] = $obj->right_delimiter;
			$this->_params['auto_encode'] = $obj->auto_encode;
			$this->_params['auto_encode_data'] = $obj->_auto_encode_data;
			$this->_params['type_script'] = $obj->_type_script;
			$this->_params['ads'] = $obj->ads;
			$this->_params['key'] = $obj->key;
			$this->_params['powered_url'] = $obj->_powered_url;
			$this->_params['carrier'] = $obj->_objAgent->getCarrierShortName();
			$this->_params['carrier_v'] = $obj->_objAgent->getCarrierShortNameAddVoda3G();
			$this->_params['objAgent'] = $obj->_objAgent;
		}
	}
	
	// }}}
	// {{{ set()
	
	/**
	 * setアクセサ
	 *
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	void
	 */
	function set($key, $value)
	{
		$this->_params[$key] = $value;
	}
	
	// }}}
	// {{{ get()
	
	/**
	 * getアクセサ
	 *
	 * @param	string	$key
	 * @return	mixed	$this->_params[$key]
	 */
	function get($key)
	{
		return $this->_params[$key];
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>