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

// OAuth ADD
// Oauthの認証チェック
$hook['post_controller_constructor'] = array(
	'class'		=> 'Terminal',
	'function'	=> 'oauth_check',
	'filename'	=> 'terminal.php',
	'filepath'	=> 'hooks'
);

// AMFPHP ADD
// 出力の変更
/*
$hook['display_override'][] = array(
	'class'		=> 'Amfphp',
	'function'	=> 'output',
	'filename'	=> 'amfphp.php',
	'filepath'	=> 'hooks'
);
*/

// jsonカスタム出力
$hook['display_override'][] = array(
	'class'		=> 'E_Json',
	'function'	=> 'output',
	'filename'	=> 'e_json.php',
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