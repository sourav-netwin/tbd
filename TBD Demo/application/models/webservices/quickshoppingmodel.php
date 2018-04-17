<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:26-08-2015
 * Dependency: None
 */

class QuickShoppingmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function save_list($user_id,$data) {

        $this->db->select(array('Id'));
        $this->db->from('userquickshoppinglist');
        $this->db->where(array(
            'UserId' => $user_id
        ));

        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            $this->db->insert('userquickshoppinglist', $data);
            return $this->db->insert_id();
        } else {
            $this->db->where('UserId', $user_id);
            $this->db->update('userquickshoppinglist', $data);
            return TRUE;
        }
    }

    public function get_list($user_id) {
        $this->db->select(array('ShoppingList'));
        $this->db->from('userquickshoppinglist');
        $this->db->where(array(
            'UserId' => $user_id
        ));

        $this->db->limit(1);
        $query = $this->db->get();
        //echo $this -> db -> last_query();die;
        return $query->row_array();
    }
}
?>