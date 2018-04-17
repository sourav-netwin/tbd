<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:05-09-2015
 * Dependency: None
 */

class Contentmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 05-09-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_menu_content($menu_id)
    {
        $this->db->select('content.Content');
        $this->db->from('content');
        $this->db->join('menu','menu.Id = content.MenuId');
        $this->db->where('menu.Id', $menu_id );
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }
    
    /* Function to get cards */
    public function get_cards() {
        $this -> db -> select('Id,CardTitle, CardImage, CardDescription')
            -> from('cards')
            -> where(
                array(
                    'IsActive' => 1,
                    'IsRemoved' => 0
                ))
            -> order_by('Sequence');
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            return $query -> result_array();
        }
        return array();
    }

    
}
