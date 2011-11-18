<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class E_Input extends CI_Input {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	* Fetch an item from the REQUEST array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function request($index = NULL, $xss_clean = FALSE)
	{
		// Check if a field has been provided
		if ($index === NULL AND ! empty($_REQUEST))
		{
			$request = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_REQUEST) as $key)
			{
				$request[$key] = $this->_fetch_from_array($_REQUEST, $key, $xss_clean);
			}
			return $request;
		}
		
		return $this->_fetch_from_array($_REQUEST, $index, $xss_clean);
	}
}

// END E_Input class

/* End of file E_Input.php */
/* Location: ./application/core/E_Input.php */
