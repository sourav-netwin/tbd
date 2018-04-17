<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

class CI_Template {

    public function __construct($params = array()) {
        // construct
    }

    // --------------------------------------------------------------------
    // Event Code
    // --------------------------------------------------------------------

    /**
     * load
     *
     * Outputs a load give view with required header and footer template
     *
     * @access	public
     * @param	string	Name of the view
     * @return	string
     */
    function view($child_view_to_load = '', $data = array()) {
        $CI = &get_instance();
        $CI->load->model('admin/adminmodel', '', TRUE);
        $data['nav_menus'] = $CI->adminmodel->load_menu();

        $data['child_view_to_load'] = $child_view_to_load;
        $CI->load->view('admin/template', $data);
    }

    /**
     * Load front end views
     *
     * Outputs a load given view with required header and footer template
     *
     * @access	public
     * @param	string	Name of the view
     * @param   Array   Additional parameters
     * @param   int     1 - Display category sub header, 0 - Do not display category sub header
     * @return	string
     */
    function front_view($child_view_to_load = '', $data = array(), $sub_header = 0 ) {
        $CI = &get_instance();
        $data['child_view_to_load'] = $child_view_to_load;
        $data['sub_header'] = $sub_header;
        $CI->load->view('front/template', $data);
    }

}