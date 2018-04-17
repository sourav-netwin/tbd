<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Class to extend default form validation functions
 * @author Arunsankar S
 * @since 04-03-2016
 */
class MY_Form_validation extends CI_Form_validation {

    public $CI;
	public function __construct($rules = array()) {
		parent::__construct($rules);
	}

	/**
	 * Function to return error array. Key will be element name
	 * @return array
	 */
	public function error_array() {
		return $this -> CI -> _error_array;
	}

}