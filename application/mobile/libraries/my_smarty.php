<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("Emo/Emo.php");
require_once("Smarty-2.6.18/Smarty.class.php");

require_once("Smarty_helper.php");

/**
* @file system/application/libraries/Mysmarty.php
*/
class My_smarty extends Smarty {

	var $config;
	var $CI;
	
	var $controller_name;
	var $method_name;
	
	var $bgcolor;
	
	var $helper;
	
	function __construct()
	{
		$this->config =& get_config();
		$this->CI     =& get_instance();
		
		$this->helper = new Smarty_helper();
		
		$this->left_delimiter = '{';
		$this->right_delimiter = '}';
		
		// absolute path prevents "template not found" errors
		$this->template_dir = (!empty($this->config['smarty_template_dir']) ? $this->config['smarty_template_dir'] 
																	  		: APPPATH.'views/');
		
		//use CI's cache folder
		$this->compile_dir  = (!empty($this->config['smarty_compile_dir']) ? $this->config['smarty_compile_dir'] 
																	 		: APPPATH.'cache/');
		
		// URL helper required
		$this->assign("SITE_URL", site_url()); // so we can get the full path to CI easily
		$this->assign("BASE_URL", base_url()); // so we can get the full path to CI easily
		
		$this->assign('helper', new Smarty_helper());
		
		$this->controller_name = $this->CI->uri->segment(1, 'home');
		$this->method_name = $this->CI->uri->segment(2, 'index');
		
		Log::DebugOut("controller_name:".$this->controller_name);
		Log::DebugOut("method_name:".$this->method_name);
		
		$this->bgcolor = $this->config['COLOR_BG_STANDARD'];
		Log::DebugOut("bgcolor:".$this->bgcolor);
		
		$this->Smarty();
	}
	
	/**
	* @param $template string
	* @param $params array holds params that will be passed to the template
	* @desc loads the template
	*/
	function view($template, $params = array())
	{
		Log::DebugOut("view(template:".$template.")");
		Log::ObjectDataOut($params);
		
		if (strpos($template, '.') === false) {
			$template .= '.tpl';
		}
		
		if (is_array($params) && count($params)) {
		
			Log::DebugOut("assign params !!!");
			
			foreach ($params as $key => $value) {
				Log::DebugOut("key:".$key.", val:".$value);
				$this->assign($key, $value);
			}
		}
		
		// check if the template file exists.
		if (!is_file($this->template_dir.$template)) {
			show_error("template: [$template] cannot be found.");
		}
		
		$this->render_header();
		
		if (($this->config['CARRIER'] === SOFTBANK) || ($this->config['CARRIER'] === WILLCOM)) {
			//$this->register_outputfilter('UTF_8toSJIS_Encoding');
		}
		else {
			$this->register_outputfilter('SJIStoUTF8_Encoding');
		}
		
		$this->register_outputfilter("EmoOutputFilter");
		
		$this->CI->output->append_output($this->fetch($template));
		
		$this->render_footer();
		
		return;
	}
	
	function render_header()
	{
		$m_mode = strtolower($this->controller_name);
		
		//$this->assign('TITLE_NAME', $this->config['TITLE_NAME']);
		
		$this->assign('LINK', $this->config['LINK']);
		$this->assign('FOCUS', $this->config['FOCUS']);
		$this->assign('VISIT', $this->config['VISIT']);
		$this->assign('BORDER_COLOR', $this->config['BORDER_COLOR']);
		
		$bg_list = $this->config['COLOR_BACKGROUND'];
		
		if (array_key_exists($m_mode, $bg_list)) {
			$this->bgcolor = $bg_list[$m_mode];
		}
		
		$this->assign('BG_COLOR', $this->bgcolor);
		
		$this->CI->output->append_output($this->fetch('header.tpl'));
	}
	
	function render_footer()
	{
		$this->CI->output->append_output($this->fetch('footer.tpl'));
	}
}

// END class smarty_library