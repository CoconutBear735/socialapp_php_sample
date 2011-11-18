<?php if (!defined ('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/OAuth.php');

class ServerSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {

	// 公開鍵
	public $cert;
	
	// 指定したコンテナの公開鍵を設定
	public function set_public_cert($consumerKey)
	{
		Log::DebugOut("set_public_cert(consumerKey:".$consumerKey.")");
		
		$CI =& get_instance();
		
		$platfrom_info = $CI->config->item('publickeys');
		$key_info = $platfrom_info[$consumerKey];
		
		Log::DebugOut("pkey:".$key_info['publickey']);
		
		if ($key_info['publickey']) {
			$this->cert = $key_info['publickey'];
			return true;
		}
		else {
			return false;
		}
	}
	
	// 公開鍵返す
	protected function fetch_public_cert(&$request)
	{
		return $this->cert;
	}
	
	protected function fetch_private_cert(&$request)
	{
		return;
	}
}

class Signed_request_validator {

	private $oauth_consumer_key;
	private $oauth_signature;
	private $gadget_url;
	private $opensocial_app_url;
	
	var $post;
	var $get;
	
	function __construct()
	{
		Log::DebugOut("SignedRequestValidator::__construct");
		
		// ネイティブオブジェクト
		$this->CI =& get_instance();
		
		$this->opensocial_app_url = $this->CI->input->request('opensocial_app_url');
		$this->oauth_consumer_key = $this->CI->input->request('oauth_consumer_key');
		$this->oauth_signature    = $this->CI->input->request('oauth_signature');
		
		$this->post = $this->CI->input->post();
		
		Log::ObjectDataOut($this->post);
		
		$this->get  = $this->CI->input->get();
		
		Log::ObjectDataOut($this->CI->input->request());
	}
	
	public function validate_request($in_url = '')
	{
		Log::DebugOut('validate_request(in_url:'.$in_url.')');
		
		$result = TRUE;
		
		// Is gadget_url specified?
		if (sizeof($in_url) > 0) {
		
			$this->gadget_url = $in_url;
		
			// Does gadget_url match opensocial_app_id?
			if ($this->opensocial_app_url != $this->gadget_url) {
				$result = FALSE;
			}
		}
		
		// Is this a signed request?
		if (!empty($this->oauth_consumer_key) && !empty($this->oauth_signature)) {
		
			$request = OAuthRequest::from_request(null, null, array_merge($this->get, $this->post));
			
			$signature_method = new ServerSignatureMethod();
			
			$signature_method->set_public_cert($this->oauth_consumer_key);
			
			// See if signature is valid
			if (!$signature_method->check_signature($request, null, null, $this->oauth_signature)) {
				$result = FALSE;
			}
		}
		else {
			$result = FALSE;
		}
		
		Log::DebugOut('problem result:'.$result);
		
		// If valid request, go forward
		return $result;
	}
}
