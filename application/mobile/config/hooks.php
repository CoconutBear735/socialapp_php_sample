<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// routeチェック
$hook['post_controller_constructor'][] = array(
	'class'		=> 'Add_param',
	'function'	=> 'param_check',
	'filename'	=> 'add_param.php',
	'filepath'	=> 'hooks'
);

// OAuth ADD
// Oauthの認証チェック
$hook['post_controller_constructor'][] = array(
	'class'		=> 'Terminal',
	'function'	=> 'oauth_check',
	'filename'	=> 'terminal.php',
	'filepath'	=> 'hooks'
);

// 携帯用出力
$hook['display_override'][] = array(
	'class'		=> 'Smarty_output',
	'function'	=> 'output',
	'filename'	=> 'smarty_output.php',
	'filepath'	=> 'hooks'
);

// 携帯用出力
$hook['display_override'][] = array(
	'class'		=> 'Flash_output',
	'function'	=> 'output',
	'filename'	=> 'flash_output.php',
	'filepath'	=> 'hooks'
);

// callback用
$hook['display_override'][] = array(
	'class'		=> 'E_Background',
	'function'	=> 'output',
	'filename'	=> 'e_background.php',
	'filepath'	=> 'hooks'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */