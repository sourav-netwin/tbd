<?php

/*
 * Author: Name:AS
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:06-01-2017
 * Dependency: None
 */

class Cardmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get all sliders that are not deleted
    public function get_cards() {
        //$this -> db -> select("Id, Image, Text, Color, IsActive, Sequence")
        $this -> db -> select("Id,CardTitle, CardImage, CardDescription, Sequence,IsActive")
            -> from('cards')
            -> where(array(
                'IsRemoved' => 0
                )
            )
            -> order_by('Sequence');
        $query = $this -> db -> get();

        return $query -> result_array();
    }

    // Get max sequence of slider
    public function get_sequence() {
        $this -> db -> select("IF(ISNULL( Max(Sequence) ) , 0 , Max(Sequence)) as Sequence", false);
        $this -> db -> limit(1);
        $query = $this -> db -> get('cards');

        return ( $query -> row() -> Sequence + 1 );
    }

    // Insert slider to database
    public function add_card($data) {

        $this -> db -> insert('cards', $data);
        return $this -> db -> insert_id();
    }

    // Delete slider
    public function delete_card($card_id) {

        $data = array('IsRemoved' => "1");
        $this -> db -> where('Id', $card_id);
        $this -> db -> update('cards', $data);

        return TRUE;
    }

    // Change status of a slider
    public function change_status($card_id, $status) {
        $data = array('IsActive' => $status);
        $this -> db -> where('Id', $card_id);
        $this -> db -> update('cards', $data);
        return TRUE;
    }

    // Get max and min sequence of slider
    public function get_max_min_sequence() {
        $this -> db -> select("IF(ISNULL( Max(Sequence) ) , 0 , Max(Sequence)) as max_sequence, IF(ISNULL( Min(Sequence) ) , 0 , Min(Sequence)) as min_sequence", false);
        $this -> db -> limit(1);
        $query = $this -> db -> get('cards');

        return $query -> row_array();
    }

    // Change sequence of slider
    public function update_card_sequence($id, $type, $update_array) {
        foreach ($update_array as $val) {
            $val_arr = explode(':', $val);
            $this -> db -> update("cards", array("Sequence" => $val_arr[1]), array("Id" => $val_arr[0]));
        }
        /* $this->db->select("Id, Sequence");
          $this->db->from("slider");
          $this->db->where( array('Id' => $id ) );
          $this->db->limit(1);
          $query = $this->db->get();
          $row = $query->row_array();

          if( $type == "up" )
          {
          $this->db->select("Id, Sequence");
          $this->db->from("slider");
          $this->db->where('Sequence <', $row['Sequence'] );
          $this->db->order_by('Sequence','Desc');
          $this->db->limit(1);
          $query = $this->db->get();
          $row_new = $query->row_array();
          }
          else
          {
          $this->db->select("Id, Sequence");
          $this->db->from("slider");
          $this->db->where('Sequence >', $row['Sequence']);
          $this->db->order_by('Sequence','Asc');
          $this->db->limit(1);
          $query = $this->db->get();
          $row_new = $query->row_array();
          }

          $this->db->update("slider", array( "Sequence" => $row['Sequence'] ), array("Id" => $row_new['Id'] ) );
          $this->db->update("slider", array( "Sequence" => $row_new['Sequence'] ), array("Id" => $row['Id'] ) ); */
    }

    public function get_card_details($id) {
        $this -> db -> from('cards');
        $this -> db -> where('Id', $id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();

        if ($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else {
            return FALSE;
        }
    }

    public function update_card_image($id, $data) {

        $this -> db -> where('Id', $id);
        $this -> db -> update('cards', $data);
    }
}