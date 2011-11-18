<?php

// {{{ Emo_Code_Core

/**
 * 絵文字コードコアクラス
 */
class Emo_Code_Core
{
	/**#@+
	 * @access public
	 */
	
	// {{{ getRecode()
	
	/**
	 * コードから１レコード取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	array	$all[$code]		絵文字レコード
	 */
	function getRecode($code)
	{
		// 配列の時はそのまま返す
		if (is_array($code)) {
			return $code;
		} else {
			$all = $this->getAll();
			return $all[$code];
		}
	}
	
	// }}}
	// {{{ getName()
	
	/**
	 * 絵文字名取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	string	$row['name']	絵文字名		例）晴れ
	 */
	function getName($code)
	{
		$row = $this->getRecode($code);
		return $row['name'];
	}
	
	// }}}
	// {{{ getNo()
	
	/**
	 * 絵文字番号取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	string	$row['no']		絵文字番号		例）1
	 */
	function getNo($code)
	{
		$row = $this->getRecode($code);
		return $row['no'];
	}
	
	// }}}
	// {{{ getSJIS()
	
	/**
	 * 絵文字sjis取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	string	$row['sjis']	絵文字sjis		例）F89F
	 */
	function getSJIS($code)
	{
		$row = $this->getRecode($code);
		return $row['sjis'];
	}
	
	// }}}
	// {{{ getUTF8()
	
	/**
	 * 絵文字utf-8取得
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	string	$row['utf-8']	絵文字utf-8		例）EE98BE
	 */
	function getUTF8($code)
	{
		$row = $this->getRecode($code);
		return $row['utf-8'];
	}
	
	// }}}
	// {{{ isCode()
	
	/**
	 * 正しい絵文字コードかどうか判定
	 *
	 * @param	string	$code			絵文字コード	例）E63E
	 * @return	boolean									例）0, 1
	 */
	function isCode($code)
	{
		$row = $this->getRecode($code);
		if ($row) {
			return 1;
		} else {
			return 0;
		}
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>