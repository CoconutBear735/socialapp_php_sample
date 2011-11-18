<?php if (!defined ('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/OAuth.php');

class Request_to_partner_server {
	
	// GET����擾
	private $opensocial_app_id;
	private $opensocial_owner_id;
	
	// �v���b�g�t�H�[�����甭�s
	private $consumer_key;
	private $consumer_secret;
	
	private $gadget_url;
	private $opensocial_app_url;
	
	var $post;
	var $get;
	
	function __construct($params = FALSE)
	{
		Log::DebugOut("Request_to_partner_server::__construct()");
		Log::ObjectDataOut($params);
		
		if ($params === FALSE) {
			return FALSE;
		}
		
		// �l�C�e�B�u�I�u�W�F�N�g
		$this->CI =& get_instance();
		
		$this->consumer_key = $params['consumer_key'];
		$this->consumer_secret = $params['consumer_secret'];
		
		$this->opensocial_app_id   = $this->CI->input->get('opensocial_app_id');
		$this->opensocial_owner_id = $this->CI->input->get('opensocial_owner_id');
		
		$this->post = $this->CI->input->post();
		
		Log::ObjectDataOut($this->post);
		
		$this->get  = $this->CI->input->get();
		
		Log::ObjectDataOut($this->CI->input->request());
	}
	
	// Gadget �T�[�o�[����p�[�g�i�[�T�[�o�[�ւ̃��N�G�X�g����
	function check_server_signature()
	{
		Log::DebugOut("check_server_signature()");
		
		$result = TRUE;
		
		$reqest = OAuthRequest::from_request(NULL, NULL, NULL);
		
		$consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
		
		$token = new OAuthToken(
			$reqest->get_parameter('oauth_token'),
			$reqest->get_parameter('oauth_token_secret')
		);
		
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		
		$oauth_signature = $reqest->get_parameter('oauth_signature');
		
		if (!$signature_method->check_signature($reqest, $consumer, $token, $oauth_signature)) {
			$result = FALSE;
		}
		
		Log::DebugOut('problem result:'.$result);
		
		return $result;
	}
}
