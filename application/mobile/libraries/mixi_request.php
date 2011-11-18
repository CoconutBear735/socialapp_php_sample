<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Log::DebugOut("include->mixi_request.php");

class Mixi_request {

	var $config;
	
	var $container_info;
	var $consumer_key;
	var $consumer_secret;
	
	var $default_url;
	
	function __construct()
	{
		$this->config =& get_config();
		
		$this->container_info = $this->config['container_info'];
		
		$this->consumer_key = $this->container_info['CONSUMER_KEY'];
		$this->consumer_secret = $this->container_info['CONSUMER_SECRET'];
		
		$this->default_url = 'http://mixi.jp/run_appli.pl?id='.$this->container_info['APP_ID'];
	}
	
	// PresonAPI ユーザ情報の取得関数
	function get_personal_info($target_id, $requestor_id = NULL, $iid = false)
	{
		Log::DebugOut("Mixi_request::get_personal_info(target_id:".$target_id.", requestor_id:".$requestor_id.", iid:".$iid.")");
		
		// Establish an OAuth consumer based on our admin 'credentials'
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// Setup OAuth request based our previous credentials and query
		$user = ($requestor_id == NULL) ? $target_id : $requestor_id;
		
		$path = ($user == $target_id) ? '/people/@me/@self' : "/people/{$target_id}/@self";
		
		$base_feed = $this->container_info['RESTFUL_URL'].$path;
		
		$params = array('xoauth_requestor_id' => $user);
		
		// ユーザハッシュ追加
		if ($iid) {
			$params['fields'] = 'userHash';
		}
		
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $base_feed, $params);
		
		// Sign the constructed OAuth request using HMAC-SHA1
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
		
		// Make signed OAuth request to the Contacts API server
		$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		Log::DebugOut("url:".$url);
		
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		
		if ($auth_header) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
		}
		
		$response = curl_exec($curl);
		
		if (!$response) {
		
			Log::DebugOut("curl error !!");
			
			curl_close($curl);
			
			return FALSE;
		}
		
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		Log::DebugOut("result_code:".$result_code);
		
		curl_close($curl);
		
		$result = json_decode($response, true);
		Log::ObjectDataOut($result);
		
		$result['response_code'] = $result_code;
		
		return ($result_code == 401) ? FALSE : $result;
	}
	
	// FriendsAPI ﾏｲﾐｸ確認関数
	function is_friend($requestor_id, $cheker_id, $hasApp = true)
	{
		Log::DebugOut("Mixi_request::is_friend(requestor_id:".$requestor_id.", cheker_id:".$cheker_id.", hasApp:".$hasApp.")");
		
		// Establish an OAuth consumer based on our admin 'credentials'
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// Setup OAuth request based our previous credentials and query
		$user = $requestor_id;
		
		$base_feed = $this->container_info['RESTFUL_URL'].'/people/@me/@friends/'.$cheker_id;
		
		$params = array('xoauth_requestor_id' => $user);
		
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $base_feed, $params);
		
		// Sign the constructed OAuth request using HMAC-SHA1
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
		
		// Make signed OAuth request to the Contacts API server
		$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		
		if ($auth_header) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
		}
		
		$response = curl_exec($curl);
		
		if (!$response) {
		
			Log::DebugOut("curl_error !!!");
			
			curl_close($curl);
			
			return FALSE;
		}
		
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		Log::DebugOut("result_code:".$result_code);
		
		curl_close($curl);
		
		$result = json_decode($response, true);
		Log::ObjectDataOut($result);
		
		// 成功＿調査ユーザはﾏｲﾐｸ
		if ($result_code == 200 || $result_code == 201) {
		
			// 一部情報を抽出
			if ($result['entry'] != null) {
				
				$name = $result['entry']['nickname'];
			}
			else {
				return FALSE;
			}
			
			return $name;
		}
		// 該当ユーザはﾏｲﾐｸではない
		else {
			return FALSE;
		}
	}
	
	// FriendsAPI ﾏｲﾐｸ情報の取得
	function get_friends($requestor_id, $hasApp = true, $opt = array())
	{
		Log::DebugOut("Mixi_request::get_friends(requestor_id:".$requestor_id.", hasApp:".$hasApp.", opt)");
		
		// Establish an OAuth consumer based on our admin 'credentials'
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// Setup OAuth request based our previous credentials and query
		$user = $requestor_id;
		
		$base_feed = $this->container_info['RESTFUL_URL'].'/people/@me/@friends';
		
		$params = array_merge($opt, array('xoauth_requestor_id' => $user));
		
		// フォーマットを明示的に指定
		$params['format'] = 'json';
		
		// インストールしているユーザのみ
		if ($hasApp == true) {
			$params['filterBy'] = 'hasApp';
		}
		
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $base_feed, $params);
		
		// Sign the constructed OAuth request using HMAC-SHA1
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
		
		// Make signed OAuth request to the Contacts API server
		$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		
		if ($auth_header) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
		}
		
		$response = curl_exec($curl);
		
		if (!$response) {
		
			Log::DebugOut("curl_error !!!");
			
			curl_close($curl);
			
			return FALSE;
		}
		
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		Log::DebugOut("result_code:".$result_code);
		
		curl_close($curl);
		
		$result = json_decode($response, true);
		
		if ($result['entry'] != null) {
			Log::ObjectDataOut($result['entry']);
		}
		
		return $result;
	}
	
	// Activity API
	function send_activity($requester_id, $in_title = FALSE, $in_url = FALSE)
	{
		Log::DebugOut("Mixi_request::send_activity(requester_id:".$requester_id.", in_title:".$in_title.", in_url:".$in_url.")");
		
		// error処理
		if ($in_title === FALSE) {
			return FALSE;
		}
		
		if ($in_url === FALSE) {
			$in_url = $this->default_url;
		}
		
		// Establish an OAuth consumer based on our admin 'credentials'
		// カスタマー用クラスを作成、カスタマーキーとカスタマーシークレットを登録
		// キーとシークレットはシグネチャーを作成するための暗号キーとして使用
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// Setup OAuth request based our previous credentials and query
		$user = $requester_id;
		
		// アクセスＵＲＬのホスト部分の作成
		$base_feed = $this->container_info['RESTFUL_URL'].'/activities/@me/@self/@app';
		Log::DebugOut('base_url:'.$base_feed);
		
		// 取得したい情報をフィールドクエリーとして設定
		$params = array('xoauth_requestor_id' => $user);
		
		// リクエストヘッダーから必要な情報を取得するために、仮に作成
		$temp = OAuthRequest::from_request('POST', $base_feed);
		
		$tok = new OAuthToken($temp->get_parameter("oauth_token"), $temp->get_parameter("oauth_token_secret"));
		
		// Proxyモデルのためトークンは必須
		//$request = OAuthRequest::from_consumer_and_token($consumer, $tok, 'POST', $base_feed, $params);
		$request = OAuthRequest::from_consumer_and_token($consumer, null, 'POST', $base_feed, $params);
		
		// Proxy モード
		//    viewer の guid ( Gadgets Server から送られて来た opensocial_viewer_id の値 ) 
		//$request->set_parameter("xoauth_requestor_id", $temp->get_parameter("opensocial_viewer_id"));
		
		// Sign the constructed OAuth request using HMAC-SHA1
		// Proxyモデルのためトークンは必須
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $tok);
		
		// シークレットトークンは削除
		$request->unset_parameter("oauth_token_secret");
		
		// POSTデータの作成
		$work_str = $in_title;
		$work_url = $in_url;

$postData = <<<EOD
{"url":"{$work_url}",
"title":"{$work_str}"
}
EOD;

		Log::ObjectDataOut($postData);
		
		// 送信クエリーの設定 今回はなし
		// Make signed OAuth request to the Contacts API server
		//$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		$url = $base_feed;
		Log::DebugOut("url:".$url);
		
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		//Log::DebugOut("auth_header:".$auth_header);
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $auth_header));
		
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		
		// APIサーバへのPOSTリクエスト実行		
		$response = curl_exec($curl);
		Log::ObjectDataOut($response);
		
		// レスポンスコードの確認
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		Log::DebugOut("result_code:".$result_code);
		
		curl_close($curl);
		
		// 成功/失敗=TRUE/FALSE
		if (($result_code === 200) || ($result_code === 202)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	// ﾒｯｾｰｼﾞの送信
	function send_message($receiver_id, $requester_id, $title, $feed_url, $options = null)
	{
		Log::DebugOut("Mixi_request::send_message(receiver_id:".$receiver_id.", requester_id:".$requester_id.", title:".$title.", feed_url:".$feed_url.", options:".$options.")");
		
		$images = '';
		
		if ($options) {
		
			$tmp_images = array();
			
			foreach ($options['Image'] as $image) {
				array_push($tmp_images, '{"url":"'.$image['url'].'",'.'"mimeType":"'.$image['mimeType'].'"}'."\n");
			}
			
			$images = implode(',', $tmp_images);
		}
		
		// Establish an OAuth consumer based on our admin 'credentials'
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// Setup OAuth request based our previous credentials and query
		$user = $requester_id;
		
		$base_feed = $this->container_info['RESTFUL_URL'].'/activities/@me/@self/@app';
		
		$params = array('xoauth_requestor_id' => $user);
		
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'POST', $base_feed, $params);
		
		$postData = <<<EOD
{"title":"{$title}",
"mobileUrl":"{$feed_url}",
"recipients":[{$receiver_id}],
"mediaItems":[{$images}]
}
EOD;

		Log::DebugOut($postData);
		
		// Sign the constructed OAuth request using HMAC-SHA1
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
		
		// Make signed OAuth request to the Contacts API server
		$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		
		Log::DebugOut("url:".$url);
		
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $auth_header));
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		
		$response = curl_exec($curl);
		Log::ObjectDataOut($response);
		
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		Log::DebugOut("result_code:".$result_code);
		
		curl_close($curl);
	}
	
	function implode_assoc($inner_glue, $outer_glue, $array)
	{
		Log::DebugOut("Mixi_request::implode_assoc(inner_glue:".$inner_glue.", outer_glue:".$outer_glue.", array)");
		Log::ObjectDataOut($array);
		
		$output = array();
		
		foreach($array as $key => $item) {
			$output[] = $key . $inner_glue . urlencode($item);
		}
		
		Log::ObjectDataOut($output);
		
		return implode($outer_glue, $output);
	}
	
	// mixiモバイルポイント決済関数
	function payment_mixi_point($requester_id, $callback_url, $finish_url, $entry = FALSE)
	{
		Log::DebugOut("Mixi_request::payment_mixi_point(requester_id:".$requester_id.", callback_url:".$callback_url.", finish_url:".$finish_url.", entry)");
		Log::ObjectDataOut($entry);
		
		//if (($requester_id <= 0) || ($entry === FALSE)) {
		//	return FALSE;
		//}
		
		$id	= $entry['id'];
		$name 	= $entry['name'];
		$point	= $entry['point'];
		
		// Establish an OAuth consumer based on our admin 'credentials'
		// カスタマー用クラスを作成、カスタマーキーとカスタマーシークレットを登録
		// キーとシークレットはシグネチャーを作成するための暗号キーとしてしよう
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		// ユーザーＩＤを設定
		// Setup OAuth request based our previous credentials and query
		$user =  $requester_id;
		
		$path = 'atom/mobile/point/@me';
		
		// アクセスＵＲＬのホスト部分の作成
		$base_feed = $this->container_info['API_URL'].$path;
		
		$params = array('xoauth_requestor_id' => $user);
		
		$request = OAuthRequest::from_consumer_and_token($consumer, null, 'POST', $base_feed, $params);
		
$postData = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<entry xmlns="http://www.w3.org/2005/Atom"
xmlns:app="http://www.w3.org/2007/app"
xmlns:point="http://mixi.jp/atom/ns#point">
<title />
<id />
<updated />
<author><name /></author>
<content type="text/xml">
<point:url callback_url="{$callback_url}" finish_url="{$finish_url}" />
<point:items>
<point:item id="{$id}" name="{$name}" point="{$point}" />
</point:items>
</content>
</entry>
EOD;
		Log::DebugOut($postData);
		
		// 本体ハッシュの作成
		$body_hash = base64_encode(sha1($postData, true));
		
		Log::DebugOut("body_hash:".$body_hash);
		
		$request->set_parameter("oauth_body_hash", $body_hash);
		
		// Sign the constructed OAuth request using HMAC-SHA1
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $tok);
		
		// Make signed OAuth request to the Contacts API server
		$url = $base_feed.'?'.$this->implode_assoc('=', '&', $params);
		
		Log::DebugOut("req_url:".$url);
		
		$curl = curl_init($url);
		
		// リクエスト確認に必要
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		
		$auth_header = $request->to_header();
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml;type=entry', $auth_header));
		
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		
		// APIサーバへのPOSTリクエスト実行		
		$response = curl_exec($curl);
		
		// レスポンスコードの確認
		$result_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		$ret = array();
		
		$ret['result_code'] = $result_code;
		$ret['point_code']  = 0;
		$ret['updated']     = date('Y-m-d H:i:s', time());
		$ret['direct_url']  = '';
		
		if ($result_code == 200 || $result_code == 201) {
		
			// 購入コードの取得
			preg_match("/<id>(.*?)<\/id>/i", $response, $match);
			$ret['point_code'] = $match[1];
			
			// コード生成日時
			preg_match("/<updated>(.*?)<\/updated>/i", $response, $match);
			$ret['updated'] = $match[1];
			
			// 飛ばすURL取得
			$temp = stristr($response, "<link href=\"");
			$temp = stristr($temp, "http://");
			
			Log::DebugOut("temp1:".$temp);
			
			$target = strpos($temp, "\"");
			
			$ret['direct_url'] = substr($temp, 0, $target);
		}
		
		Log::DebugOut("___mixi_point_api_end___");
		
		return $ret;
	}

}

