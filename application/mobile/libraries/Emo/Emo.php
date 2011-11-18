<?php

/**
 * PHP versions 4 and 5
 *
 * @copyright ups8
 * @package	Emo
 */

require_once(dirname(__FILE__) . "/Emo/UserAgent.php");
require_once(dirname(__FILE__) . "/Emo/Convert.php");
require_once(dirname(__FILE__) . "/Emo/Utility.php");
require_once(dirname(__FILE__) . "/Emo/Code/docomo.php");
require_once(dirname(__FILE__) . "/Emo/Code/EZweb.php");
require_once(dirname(__FILE__) . "/Emo/Code/SoftBank.php");
require_once(dirname(__FILE__) . "/Emo/Context.php");
require_once(dirname(__FILE__) . "/Emo/Encode.php");
require_once(dirname(__FILE__) . "/Emo/Decode.php");
require_once(dirname(__FILE__) . "/Emo/In.php");
require_once(dirname(__FILE__) . "/Emo/Dialog.php");

// {{{ constants

/* バージョン定義 */
define('Emo_VERSION', '3.1.1');

// }}}
// {{{ Emo

/**
 * 絵文字クラスライブラリ
 */
class Emo
{
	// {{{ properties
	
	/**#@+
	 * @access public
	 */
	
	/**
	 * 絵文字画像ディレクトリパスです。
	 * 携帯絵文字をPCで閲覧したときの代替画像になります。
	 * デフォルトは'./libs/img'です。
	 *
	 * @var string
	 */
	var $img_dir = "./libs/img";
	
	/**
	 * 絵文字コードの開始を表すデリミタです。
	 * デフォルトは'[['です。
	 *
	 * @var string
	 */
	var $left_delimiter = '[[';
	
	/**
	 * 絵文字コードの終了を表すデリミタです。
	 * デフォルトは']]'です。
	 *
	 * @var string
	 */
	var $right_delimiter = ']]';
	
	/**
	 * 各キャリアの絵文字の文字コード
	 * 右に設定値一覧を示す。(*)がデフォルト
	 *
	 * @var string
	 */
	var $type_set_docomo   = "sjis";		// sjis(*), utf-8
	var $type_get_docomo   = "sjis";		// sjis(*), utf-8
	var $type_set_EZweb    = "sjis";		// sjis(*), utf-8
	var $type_get_EZweb    = "sjis";		// sjis(*), utf-8, tag
	var $type_set_SoftBank = "unicode";		// web(*), sjis, utf-8 special customize unicode
	var $type_get_SoftBank = "unicode";		// web(*), sjis, utf-8 special customize unicode
	var $type_set_Voda3G   = "unicode";		// web(*), sjis, utf-8 special customize unicode
	var $type_get_Voda3G   = "unicode";		// web(*), sjis, utf-8 special customize unicode
	
	/**
	 * 各キャリアの文字コード
	 * 右に設定値一覧を示す。(*)がデフォルト
	 *
	 * @var string
	 */
	var $type_in_docomo    = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_out_docomo   = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_in_EZweb     = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_out_EZweb    = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_in_SoftBank  = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_out_SoftBank = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_in_Voda3G    = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_out_Voda3G   = "sjis";		// sjis(*), utf-8, euc-jp, 他
	var $type_in_pc        = "sjis";		// sjis(*), utf-8, euc-jp, 他	pc時html入力
	var $type_out_pc       = "sjis";		// sjis(*), utf-8, euc-jp, 他	pc時html出力	支援ツールhtml
			
	/**
	 * 呼び出し元の文字コード
	 * 右に設定値一覧を示す。(*)がデフォルト
	 *
	 * @var string
	 */
	var $type_get_base     = "sjis";		// sjis(*), utf-8, euc-jp, 他	入力変換で使用
	var $type_set_base     = "sjis";		// sjis(*), utf-8, euc-jp, 他	出力変換で使用
	
	/**
	 * 文字コードの自動エンコードフラグです。
	 * 受信した文字の文字コードを自動で判定し、適切な文字コードで受け取るようにします。
	 * デフォルトはtrueです。
	 * </form>を含むHTMLをdecode()表示 → 
	 * request(), get(), post()で受信した場合のみ適応されます。
	 * ただし、$type_in~で設定した文字コードは一切無視されます。
	 *
	 * @var boolean
	 */
	var $auto_encode = true;
	
	/**
	 * ユーザーエイジェント
	 * デフォルトは未設定です。
	 *
	 * @var string
	 */
	var $userAgent;
	
	/**
	 * 広告
	 *
	 * @var boolean
	 */
	var $ads = true;
	
	/**
	 * プロダクトキー
	 */
	var $key = "ccc";
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	//--------------------------------------------------
	/**
	 * ここからから下は制御用
	 */
	//--------------------------------------------------
	 
	/**
	 * コンテキスト
	 *
	 * @var context
	 */
	var $_context;
	
	/**
	 * 本スクリプトソースの文字コード
	 *
	 * @var string
	 */
	var $_type_script = "utf-8";	// utf-8(*), sjis, euc-jp, 他
	
	/**
	 * 文字コードの自動エンコード判定文字列
	 *
	 * @var array
	 */
	var $_auto_encode_data = array(
			"name" => "EmoAutoEncodeStr",
			"value" => "Emo自動エンコード文字列",
		);
	
	/**
	 * ユーザーエイジェントインスタンス
	 *
	 * @var userAgent
	 */
	var $_objAgent;
	
	/**
	 * powered by url
	 *
	 * @var string
	 */
	var $_powered_url = "http://gard.no-ip.info/emo/";
	
	var $_option;
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 */
	function Emo( $option = 1)
	{
		$this->_option = $option;
	}
	
	// }}}
	// {{{ encode()
	
	/**
	 * エンコード
	 * モバイルコードから抽象絵文字コードへ（キャリアは自動判定）
	 * 文字コードも変換
	 *
	 * @param	string	$str				変換前	例）晴れ曇り
	 * @retrun	string	$c->main($str)		変換後	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 */
	function encode($str)
	{
		$this->_setProperties();
		$c =& new Emo_Encode($this->_context);
		return $c->main($str);
	}
	
	// }}}
	// {{{ decode()
	
	/**
	 * デコード
	 * 抽象絵文字コードからモバイルコードへ（キャリアは自動判定）
	 * 文字コードも変換
	 *
	 * @param	string	$str				変換前	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 * @return	string	$c->main($str)		変換後	例）晴れ曇り
	 */
	function decode($str)
	{
		$this->_setProperties();
		$c =& new Emo_Decode($this->_context);
		return $c->main($str, $this->_option);
	}

	/**
	 * デコード
	 * 抽象絵文字コードからモバイルコードへ（キャリアは自動判定）
	 * 文字コードも変換
	 *
	 * @param	string	$str				変換前	例）晴れ[[d-E63E]]曇り[[d-E63F]]
	 * @return	string	$c->main($str)		変換後	例）晴れ曇り
	 */
	function decode2($str)
	{
		$this->_setProperties();
		$c =& new Emo_Decode($this->_context);
		return $c->main_option($str);
	}
	
	// }}}
	// {{{ request()
	
	/**
	 * requestエンコードデータ取得
	 *
	 * @return	array	$c->requeste()
	 */
	function request()
	{
		$this->_setProperties();
		$c =& new Emo_In($this->_context);
		return $c->requeste();
	}
	
	// }}}
	// {{{ get()
	
	/**
	 * getエンコードデータ取得
	 *
	 * @return	array	$c->gete()
	 */
	function get()
	{
		$this->_setProperties();
		$c =& new Emo_In($this->_context);
		return $c->gete();
	}
	
	// }}}
	// {{{ post()
	
	/**
	 * postエンコードデータ取得
	 *
	 * @return	array	$c->poste()
	 */
	function post()
	{
		$this->_setProperties();
		$c =& new Emo_In($this->_context);
		return $c->poste();
	}
	
	// }}}
	// {{{ dialogJs()
	
	/**
	 * 絵文字入力支援ダイアログJS
	 *
	 * @return	string	$c->getHead()
	 */
	function dialogJs()
	{
		$this->_setProperties();
		$c =& new Emo_Dialog($this->_context);
		return $c->getJs();
	}
	
	// }}}
	// {{{ dialogBtn()
	
	/**
	 * 絵文字入力支援ダイアログのボタン
	 *
	 * @param	string	$target							ターゲットとなるDOM名	例）document.f1.t1
	 * @param	string	$carrier						キャリア名				例）d, e, s
	 * @return	string	$c->getBtn($target, $carrier)	ボタンHTML
	 */
	function dialogBtn($target = "", $carrier = "")
	{
		$this->_setProperties();
		$c =& new Emo_Dialog($this->_context);
		return $c->getBtn($target, $carrier);
	}
	
	// }}}
	// {{{ isMobile()
	
	/**
	 * モバイル判定
	 *
	 * @return	boolean	$this->_objAgent->isMobile()	モバイルフラグ
	 */
	function isMobile()
	{
		$this->_setProperties();
		return $this->_objAgent->isMobile();
	}
	
	// }}}
	// {{{ getCarrierShortName()
	
	/**
	 * 短いキャリア名取得
	 *
	 * @return	string	$this->_objAgent->getCarrierShortName()		短いキャリア名	例）d, e, s
	 */
	function getCarrierShortName()
	{
		$this->_setProperties();
		return $this->_objAgent->getCarrierShortName();
	}
	
	// }}}
	// {{{ isVoda3G()
	
	/**
	 * Vodafone(3G判定)
	 *
	 * @return	boolean	$this->_objAgent->isVoda3G()	Voda3Gフラグ
	 */
	function isVoda3G()
	{
		$this->_setProperties();
		return $this->_objAgent->isVoda3G();
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _setProperties()
	
	/**
	 * プロパティ設定
	 *
	 * @return	void
	 */
	function _setProperties() {
		$this->_objAgent =& new Emo_UserAgent($this->userAgent);
		$this->_context =& new Emo_Context($this);
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>
