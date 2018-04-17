<?php

/*
 * Author:  PM
 * Purpose: Content related functions
 * Date:    13-10-2015
 */

class Contentmodel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    /* Function to get content
	 * Param: menu_id - Id of menu record whose content is to be returned
	 * Return: content - Content of the page
     */
    public function get_content( $menu_id )
    {
    	$this->db->select('Content');
        $this->db->from('content');
        $this->db->join('menu','menu.Id = content.MenuId');
        $this->db->where( array( 'menu.Id' => $menu_id ) );
        $this->db->limit(1);

        $query = $this->db->get();
    	return $query->row()->Content;
    }
}

?>