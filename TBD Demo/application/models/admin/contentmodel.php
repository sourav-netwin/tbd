<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:07-09-2015
 * Dependency: None
 */

class Contentmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 07-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_content_menus() {
        $this->db->select('menu.Id, menu.MenuName, content.Id as is_present');
        $this->db->from('menu');
        $this->db->join('content', 'content.MenuId = menu.Id', 'left');
        $this->db->where(
                array(
                    'menu.IsRemoved' => 0
        ));
        $this->db->order_by("sequence", "asc"); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_menu_details($menu_id) {
        $this->db->select('Id, MenuName');
        $this->db->from('menu');
        $this->db->where( 'Id', $menu_id );
        $this->db->limit(1);
        $query = $this->db->get();
     
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function get_menu_content( $menu_id )
    {
        $this->db->select('content.*');
        $this->db->from('content');
        $this->db->join('menu','menu.Id = content.MenuId');
        $this->db->where( 'menu.Id', $menu_id );
        $this->db->limit(1);
        $query = $this->db->get();
     
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function update_content($menu_id,$data) {

        $this->db->where('MenuId', $menu_id);
        $this->db->update('content', $data);
    }

    public function add_content( $data ) {
        $this->db->insert('content', $data);
        return $this->db->insert_id();
    }
}
?>