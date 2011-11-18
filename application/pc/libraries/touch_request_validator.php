<?php if (!defined ('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/OAuth.php');

class Touch_request_validator {

	private $oauth_consumer_key;
	private $oauth_signature;
	private $gadget_url;
	private $opensocial_app_url;
	
	var $post;
	var $get;
	
	function __construct($referer_arg)
	{
		Log::DebugOut("Touch_request_validator::__construct");
		
		// ネイティブオブジェクト
		$this->CI =& get_instance();
		
		$this->oauth_consumer_key = 'mixi.touch';
		$this->oauth_signature    = $referer_arg['oauth_signature'];
		
		$this->post = $this->CI->input->post();
		
		if ($this->post === FALSE) {
			$this->post = array();
		}
		
		$this->get = $this->CI->input->get();
		
		if ($this->get === FALSE) {
			$this->get = array();
		}
		
		Log::ObjectDataOut($this->get);
		Log::ObjectDataOut($this->post);
		Log::ObjectDataOut($this->CI->input->request());
	}
	
	public function validate_request($in_url = '')
	{
		Log::DebugOut('validate_request(in_url:'.$in_url.')');
		
		$result = TRUE;
		
		// Is gadget_url specified?
		if (sizeof($in_url) > 0) {
			$this->gadget_url = $in_url;
		}
		
		// Is this a signed request?
		if (!empty($this->oauth_consumer_key) && !empty($this->oauth_signature)) {
		
			$request = OAuthRequest::from_request(null, null, array_merge($this->get, $this->post));
			
			$signature_method = new ServerSignatureMethod();
			
			$signature_method->set_public_cert($this->oauth_consumer_key);
			
			// See if signature is valid
			if (!$signature_method->check_signature($request, null, null, $this->oauth_signature)) {
				//$result = TRUE;
				$result = FALSE;
			}
		}
		else {
			$result = FALSE;
		}
		
		Log::DebugOut('problem result:'.$result, 1);
		
		// If valid request, go forward
		return $result;
	}
}
