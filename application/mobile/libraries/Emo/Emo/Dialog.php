<?php
require_once(dirname(__FILE__) . "/Code/docomo.php");
require_once(dirname(__FILE__) . "/Code/EZweb.php");
require_once(dirname(__FILE__) . "/Code/SoftBank.php");
require_once(dirname(__FILE__) . "/Utility.php");

// {{{ Emo_Dialog

/**
 * 絵文字入力支援クラス
 */
class Emo_Dialog {

	// {{{ properties
	
	/**#@+
	 * @access private
	 */
	
	var $_img_dir;
	var $_left_delimiter;
	var $_right_delimiter;
	var $_code_type;
	var $_type_script;
	var $_ads;
	var $_key;
	var $_powered_url;
	var $_utility;
	
	/**#@-*/
	
	/**#@+
	 * @access public
	 */
	
	// }}}
	// {{{ constructor()
	
	/**
     * constructor
	 *
	 * @param	context	$context	コンテキスト
	 */
	function Emo_Dialog(&$context)
	{
		$this->_left_delimiter = $context->get("left_delimiter");
		$this->_right_delimiter = $context->get("right_delimiter");
		$this->_img_dir = $context->get("img_dir");
		$this->_code_type = $context->get("code_type");
		$this->_type_script = $context->get("type_script");
		$this->_ads = $context->get("ads");
		$this->_key = $context->get("key");
		$this->_powered_url = $context->get("powered_url");
		$this->_utility =& new Emo_Utility;
	}
	
	// }}}
	// {{{ getJs()
	
	/**
	 * 先読みJS出力（<head><scirpt>~</script></head>に入れる用）
	 *
	 * return	string	$out	絵文字入力支援のヘッダJavascript
	 */
	function getJs()
	{
		$data_code = "_getCodeArr : function(carrier) {\nvar d = new Array();\nswitch (carrier) {\n";
		foreach (array("d","e","s") as $carrier) {
			$data_code .= "case '{$carrier}':\n";
			$codes = $this->_getCode($carrier);
			foreach ($codes as $key => $value) {
				$data_code .= "d.push(this._a('{$key}','{$value['name']}'));";
			}
			$data_code .= "break;\n";
		}
		$data_code .= "}\nreturn d;\n},\n";

// ここからJavascript--------------------------------------------------
		$out = <<<EOD

/**
 * 絵文字入力ダイアログ
 *
 * @package	Emo
 * @link {$this->_powered_url}
 */
// イベント設定
document.onmousemove = EmoMouseMove;	//マウスが移動した時のイベントハンドラ　EmoMouseMoveを実行
document.onmouseup = EmoMouseUp;		//マウスのボタンを離したとき
document.onmousedown = EmoMouseDown;	//マウスのボタンを押した時

// 絵文字JSクラスのコンストラクタ
var EmoJSClass = function() {
	this._targetName;			// 入力支援テキストのターゲット名
	this._dialogDragOn = false;		// 絵文字入力表示ダイアログのドラッグ
	this._dialogObj;
	this._offsetX;
	this._offsetY;
	this._carrierView;	// 表示してるキャリア
	this._mouseX;
	this._mouseY;
	this._dialogOverFlag = false;		// 絵文字入力表示画面タイトルバーのマウスオーバー
};
// 絵文字JSクラスのメソッド一覧
EmoJSClass.prototype = {
	
	/**#@+
	 * @access public
	 */
	
	//--------------------------------------------------
	/**
	 * 絵文字入力ダイアログ作成
	 *
	 * @param	string	target
	 * @param	string	carrier
	 * @param	boolean	tab
	 */
	createDialog : function(target, carrier, tab) {
		this._targetName = target;
		var width = new Array();
		var height = new Array();
		
		// 再度同じボタン押した時、削除
		if (this._isObj("EmoDialog") && this._carrierView == carrier) {
			this.delDialog();	//削除
			return false;
		}
		this._carrierView = carrier;
		this.delDialog();	//削除
		
		var ads = "{$this->_ads}";	// 広告
		var myStyle = "style='"
			+ "font-size:12px;"
			+ "text-align:center;"
			+ "border-width:1px;"
			+ "border-style:solid;"
			+ "background-color:white;"
			+ "line-height:150%;"
			+ "letter-spacing:1pt;"
			+ "padding-bottom:20px;"
			+ "position:absolute;"
			+ "width:"+width[carrier]+";"
			+ "height:"+height[carrier]+";"
			+ "top:100px;"
			+ "left:100px;"
			+ "'";
		var myStyle2 = "style='"
			+ "background-color:#6A8ABD;"
			+ "color:white;"
			+ "font-weight:bold;"
			+ "text-align:left;"
			+ "cursor:move;'";
		var tabStr = "";
		if (tab) {
			tabStr = "<div id='EmoTab'></div>";
		}
		var footStr = "<input type='button' value='　　閉じる　　' onClick='Javascript:EmoJS.delDialog()' />"
			+ "<br /><a href='{$this->_powered_url}' target='_blank'>powered by Emo</a>"
			+ "<!-- Emo.key={$this->_key} //-->";
		// 広告判定
		if (ads == "true" || ads == "1") {
			footStr += "<br /><a href='{$this->_powered_url}' target='_blank'><img src='{$this->_img_dir}/ads.gif' border='0'></a>";
		}
		var str = "<div id='EmoDialog' "+myStyle+" >"
			+ "<div id='EmoTitle' "+myStyle2+" onmouseover='Javascript:EmoJS.dialogOver();' onmouseout='Javascript:EmoJS.dialogOut();' title='ドラッグで移動できます'></div>"
			+ tabStr
			+ "<div id='EmoImgList'>"
			+ "<img src='{$this->_img_dir}/box_" + carrier + ".gif' border='0' usemap='#EmoImgList'>"
			+ "<map name='EmoImgList'>" + this._getAllAreaTag(carrier) + "</map>"
			+ "</div>"
			+ footStr
			+ "</div>";
		var layer = document.createElement("div");
		layer.innerHTML = str;
		document.body.appendChild(layer);
		
		// 絵文字入力ダイアログ中身変更
		this.editDialog(target, carrier, tab);
		
		// ダイアログ位置補正（window枠からはみ出る場合は上へ補正）
		if (this._clientHeight() < parseInt(this._mouseY) - this._scrollY() + parseInt(this._getObjStyle("EmoDialog").height) + (20 * tab)) {	// ダイアログが画面に入りきらなかった時
			this._getObjStyle("EmoDialog").top = parseInt(this._clientHeight()) + this._scrollY() - parseInt(this._getObjStyle("EmoDialog").height) - (20 * tab) - 10;	// ダイアログが画面内に収まるように
		} else {
			this._getObjStyle("EmoDialog").top = this._mouseY;	// デフォルト
		}
		this._getObjStyle("EmoDialog").left = this._mouseX + 50;
		
		this._selectErrorTag("hidden");	// Selectタグ非表示
	},
	//--------------------------------------------------
	/**
	 * 絵文字入力ダイアログ中身変更
	 *
	 * @param	string	target
	 * @param	string	carrier
	 * @param	boolean	tab
	 */
	editDialog : function(target, carrier, tab) {
		var str = "<img src='{$this->_img_dir}/box_" + carrier + ".gif' border='0' usemap='#EmoImgList'>"
			+ "<map name='EmoImgList'>" + this._getAllAreaTag(carrier) + "</map>";
		this._getObj("EmoImgList").innerHTML = "";	// googleChrome対策
		this._getObj("EmoImgList").innerHTML = str;
		
		var title = "&nbsp;" + this._carrierName(carrier) + "絵文字入力 -";
		this._getObj("EmoTitle").innerHTML = "";	// googleChrome対策
		this._getObj("EmoTitle").innerHTML = title;
		
		var carr = new Array("d", "e", "s");
		var tstr;
		if (tab) {
			tstr = "";
			for (i in carr) {
				tstr += "<img src='{$this->_img_dir}/tab";
				// 選択中のとき
				if (carr[i] != carrier) {
					tstr += "2";
				}
				tstr += "_"+carr[i]+".gif' border='0' onClick='Javascript:EmoJS.editDialog(\""+target+"\", \""+carr[i]+"\", 1);' alt='"+this._carrierName(carr[i])+"' /></a>"
			}
			this._getObj("EmoTab").innerHTML = tstr;
		}
		
		var width = new Array();
		var height = new Array();
		width["d"] = 242;height["d"] = 295;		// docomo
		width["e"] = 242;height["e"] = 700;		// EZweb
		width["s"] = 242;height["s"] = 580;		// SoftBank
		
		this._getObjStyle("EmoDialog").width = width[carrier];
		this._getObjStyle("EmoDialog").height = height[carrier];
		
		// ダイアログ位置補正（window枠からはみ出る場合は上へ補正）
		if (this._clientHeight() < parseInt(this._mouseY) - this._scrollY() + parseInt(this._getObjStyle("EmoDialog").height) + (20 * tab)) {	// ダイアログが画面に入りきらなかった時
			this._getObjStyle("EmoDialog").top = parseInt(this._clientHeight()) + this._scrollY() - parseInt(this._getObjStyle("EmoDialog").height) - (20 * tab) - 10  ;	// ダイアログが画面内に収まるように
		}
		this._selectErrorTag("hidden");	// Selectタグ非表示
	},
	//--------------------------------------------------
	/**
	 * ダイアログタイトルバーマウスオーバーイベント
	 */
	dialogOver : function() {
		this._dialogOverFlag = true;
	},
	//--------------------------------------------------
	/**
	 * ダイアログタイトルバーマウスアウトイベント
	 */
	dialogOut : function() {
		this._dialogOverFlag = false;
	},
	//--------------------------------------------------
	/**
	 * マウスダウンイベント
	 */
	mouseDown : function(e) {
		this._mouseX = this._getMouseX(e);
		this._mouseY = this._getMouseY(e);
		if (this._dialogOverFlag == true) {
			this._dialogDragStart(e);
		}
	},
	//--------------------------------------------------
	/**
	 * マウス移動
	 */
	mouseMove : function(e) {
		if (this._dialogDragOn == true) {
			this._dialogObj.left = 200;//this._getMouseX(e) - this._offsetX;	// オブジェクトの左からの位置 X座標
			alert(this._dialogObj.left);
			this._dialogObj.top = this._getMouseY(e) - this._offsetY;	// オブジェクトの上からの位置 Y座標
		}
	},
	//--------------------------------------------------
	/**
	 * マウスUP
	 */
	mouseUp : function() {
		this._dialogDragEnd();
	},
	//--------------------------------------------------
	// 絵文字入力ダイアログ削除
	delDialog : function() {
		this._delObj("EmoDialog");
		this._selectErrorTag("visible");	// Selectタグ表示
	},
	//--------------------------------------------------
	/**
	 * 絵文字入力
	 */
	input : function(code) {
		var myObj = eval(this._targetName);
		var text= "{$this->_left_delimiter}" + code + "{$this->_right_delimiter}";
		if (text != null && text != "") {
			myObj.focus();
			this._inputText(myObj, text);
		}
		self.focus();
	},
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	//--------------------------------------------------
	/**
	 * 絵文字コード集取得
	 */
	{$data_code}
	//--------------------------------------------------
	/**
	 * 絵文字連想オブジェクト作成
	 */
	_a : function(key, name) {
		var o = new Object();
		o.key = key;
		o.name = name;
		return o;
	},
	//--------------------------------------------------
	/**
	 * 全Areaタグ取得
	 */
	_getAllAreaTag : function(carrier) {
		var codeArr = this._getCodeArr(carrier);	// 絵文字コード集取得
		var str = "";
		for (var i in codeArr) {
			var code = codeArr[i];
			str += this._getAreaTag(i, carrier, code['key'], code['name']);
		}
		return str;
	},
	//--------------------------------------------------
	/**
	 * Areaタグ取得
	 */
	_getAreaTag : function(i, carrier, key, name) {
		var xb = new Number();
		var yb = new Number();
		var per = new Number();
		// ここから表示用
		switch (carrier) {
		case "e":
			xb = 14;yb = 15;per = 17;break;
		case "s":
			xb = 15;yb = 15;per = 16;break;
		default:
			carrier = "d";
			xb = 12;yb = 12;per = 20;break;
		}
		var x = (i % per) * xb;
		var y = Math.floor(i / per) * yb;
		var rtn = "<area shape='rect' coords='" + x + "," + y + "," + (x + xb) +"," + (y + yb) +"' href='javascript:EmoJS.input(\\"" + carrier + "-" + key + "\\");' title='" + name + "'>";
		return rtn;
	},
	//--------------------------------------------------
	/**
	 * キャリア表示名取得
	 */
	_carrierName : function(carrier) {
		if (carrier == "d") {
			return "docomo";
		} else if (carrier == "e") {
			return "EZweb";
		} else if (carrier == "s") {
			return "SoftBank";
		}
	},
	//--------------------------------------------------
	/**
	 * マウスX位置取得(絶対座標)
	 */
	_getMouseX : function(e) {
		if (window.opera) {
			return document.body.scrollLeft + e.clientX;
		} else if (document.all) {
			return document.body.scrollLeft + event.clientX;
		} else if (document.layers || document.getElementById) {
			return e.pageX;
		}
	},
	//--------------------------------------------------
	/**
	 * マウスY位置取得(絶対座標)
	 */
	_getMouseY : function(e) {
		if (window.opera) {
			return document.body.scrollTop + e.clientY;
		} else if (document.all) {
			return document.body.scrollTop + event.clientY;
		} else if (document.layers || document.getElementById) {
			return e.pageY;
		}
	},
	//--------------------------------------------------
	/**
	 * ダイアログドラッグ開始
	 */
	_dialogDragStart : function(e) {
		this._dialogObj = this._getObjStyle("EmoDialog");
		this._offsetX = this._getMouseX(e) - parseInt(this._dialogObj.left);
		this._offsetY = this._getMouseY(e) - parseInt(this._dialogObj.top);
		this._dialogDragOn = true;
	},
	//--------------------------------------------------
	/**
	 * ダイアログドラッグ終了
	 */
	_dialogDragEnd : function() {
		if (this._dialogDragOn == true) {
			this._dialogDragOn = false;
		}
	},
	//--------------------------------------------------
	/**
	 * Xスクロール位置取得
	 */
	_scrollX : function() {
		return document.body.scrollLeft || document.documentElement.scrollLeft;
	},
	//--------------------------------------------------
	/**
	 * Yスクロール位置取得
	 */
	_scrollY : function() {
		return document.body.scrollTop || document.documentElement.scrollTop;
	},
	//--------------------------------------------------
	/**
	 * 表示枠縦幅取得
	 */
	_clientHeight : function() {
		return document.body.clientHeight || document.documentElement.clientHeight;
	},
	//--------------------------------------------------
	/**
	 * オブジェクト有無判定
	 */
	_isObj : function(Lyid) {
		var myObj;
		// ブラウザ判定
		if (document.getElementById) {
			myObj = document.getElementById(Lyid);		// N6
		} else if (document.all) {
			myObj = document.all[Lyid];					// IE
		} else if (document.layers) {
			myObj = document[Lyid];						// N4
		}
		if (myObj) {
			return true;
		} else {
			return false;
		}
	},
	//--------------------------------------------------
	/**
	 * オブジェクトスタイル取得
	 */
	_getObjStyle : function(Lyid) {
		var myObj;
		// ブラウザ判定
		if (document.getElementById) {
			myObj = document.getElementById(Lyid).style;	// N6
		} else if (document.all) {
			myObj = document.all[Lyid].style;				// IE
		} else if (document.layers) {
			myObj = document[Lyid];							// N4
		}
		return myObj;
	},
	//--------------------------------------------------
	/**
	 * オブジェクト取得
	 */
	_getObj : function(Lyid) {
		var myObj;
		// ブラウザ判定
		if (document.getElementById) {
			myObj = document.getElementById(Lyid);	// N6
		} else if (document.all) {
			myObj = document.all[Lyid];				// IE
		} else if (document.layers) {
			myObj = document[Lyid];					// N4
		}
		return myObj;
	},
	//--------------------------------------------------
	/**
	 * オブジェクト削除
	 */
	_delObj : function(Lyid) {
		if (document.getElementById(Lyid)) {
			var myObj=document.getElementById(Lyid);
			var myObj_parent=myObj.parentNode;
			myObj_parent.removeChild(myObj);
		}
	},
	//--------------------------------------------------
	/**
	 * 絵文字入力コア
	 */
	_inputText : function(myObj, text) {
		if (myObj.setSelectionRange) {											// OP, FX, NS, CR
			var start = myObj.selectionStart;
			myObj.value = myObj.value.substring(0, start) +
							text + myObj.value.substring(myObj.selectionEnd, myObj.textLength);
			var sr = start + text.length;
			myObj.setSelectionRange(sr, sr);
		} else if (document.selection && document.selection.createRange) {		// IE
			document.selection.createRange().text = text;
		} else {
			myObj.value += text;
		}
	},
	//--------------------------------------------------
	// Selectタグ表示・非表示（IE6用）
	_selectErrorTag : function(dp) {
		if (navigator.userAgent.indexOf("MSIE 6") == -1) {return;}	// IE6以外は抜ける
		if (dp != "visible" && dp != "hidden") {return;}	// 表示・非表示
		var selects = document.getElementsByTagName('select');
		if (selects == null) {return;}
		for (var i = 0;i < selects.length;i++) {
			selects[i].style.visibility = dp;
		}
	}
	
	/**#@-*/
};

// EmoJSインスタンス作成
var EmoJS = new EmoJSClass;

// マウスダウンイベント
function EmoMouseDown(e) {
	EmoJS.mouseDown(e);
}
// マウス移動
function EmoMouseMove(e) {
	EmoJS.mouseMove(e);
}
// マウスUP
function EmoMouseUp() {
	EmoJS.mouseUp();
}

EOD;
// ここまでJavascript--------------------------------------------------
		// $out = $this->_simpleCode($out);
		$out = $this->_utility->mbConvertEncoding($out, $this->_code_type['out_pc'], $this->_type_script);	// 文字コード変換
		return $out;
	}
	
	// }}}
	// {{{ getBtn()
	
	//--------------------------------------------------
	/**
	 * 絵文字入力画面表示ボタン用HTML取得
	 *
	 * @param	string	$target		ターゲットDom名		例）document.f1.t1
	 * @param	string	$carrier	キャリア名			例）d, e, s
	 * @return	string	$out		ボタンHTML
	 */
	function getBtn($target, $carrier = "")
	{
		$cname = $this->_utility->carrierShortToLong($carrier);
		if ($carrier) {
		$out = <<<EOD
<a href="" onClick="Javascript:EmoJS.createDialog('{$target}', '{$carrier}', 0);return false;" title="{$cname}"><img src="{$this->_img_dir}/btn_{$carrier}.gif" border="0" alt="絵文字入力" /></a>
EOD;
		} else {
		$out = <<<EOD
<a href="" onClick="Javascript:EmoJS.createDialog('{$target}', 'd', 1);return false;" title="{$cname}"><img src="{$this->_img_dir}/btn_a.gif" border="0" alt="絵文字入力" /></a>
EOD;
		}
		$out = $this->_utility->mbConvertEncoding($out, $this->_code_type['out_pc'], $this->_type_script);	// 文字コード変換
		return $out;
	}
	
	/**#@-*/
	
	/**#@+
	 * @access private
	 */
	
	// }}}
	// {{{ _getCode()
	
	/**
	 * キャリアコード集取得
	 *
	 * @param	string		$carrier		キャリア名	例）d, e, s
	 * @return	Array		$c->getAll()	コード集
	 */
	function _getCode($carrier = "d")
	{
		$cname = $this->_utility->carrierShortToLong($carrier);
		if ($cname) {
			$class_name = "Emo_Code_" . $cname;
			$c =& new $class_name;
			return $c->getAll();
		}
	}
	
	// }}}
	// {{{ _simpleCode()
	
	/**
	 * コード縮小
	 * $this->headからのみ呼び出し
	 *
	 * @param	string		$out	縮小前文字列
	 * @return	string		$out	縮小後文字列
	 */
	function _simpleCode($out)
	{
		// $out = preg_replace("!//[^(\r\n|\r|\n)]*!", "", $out);	// urlが消える
		$out = preg_replace("!/\*[^/]*\*/!", "", $out);
		$out = preg_replace("!(\t)!", "", $out);
		// $out = preg_replace("!(\r\n|\r|\n)!", "", $out);	//なぜか動かなくなる
		return $out;
	}
	
	/**#@-*/
	
	// }}}
}

// }}}

?>