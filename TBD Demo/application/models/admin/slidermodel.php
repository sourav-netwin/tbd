<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:07-10-2015
 * Dependency: None
 */

class Slidermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 07-10-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    // Get all sliders that are not deleted
    public function get_sliders(){
        $this->db->select("Id, Image, IsActive, Sequence");
        $this->db->from('slider');
        $this->db->where('IsRemoved', '0');
        $this->db->order_by('Sequence');
        $query = $this->db->get();

        return $query->result_array();
    }

    // Get max sequence of slider
    public function get_sequence() {
        $this->db->select("IF(ISNULL( Max(Sequence) ) , 0 , Max(Sequence)) as Sequence",false);
        $this->db->limit(1);
        $query = $this->db->get('slider');

        return ( $query->row()->Sequence + 1 );
    }

    // Insert slider to database
    public function add_slider($data) {

        $this->db->insert('slider', $data);
        return $this->db->insert_id();
    }

    // Delete slider
    public function delete_slider($slider_id) {

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $slider_id);
        $this->db->update('slider', $data);

        return TRUE;
    }

    // Change status of a slider
    public function change_status($slider_id, $status) {

        $data = array('IsActive' => $status);
        $this->db->where('Id', $slider_id);
        $this->db->update('slider', $data);

        return TRUE;
    }

    // Get max and min sequence of slider
    public function get_max_min_sequence(){
        $this->db->select("IF(ISNULL( Max(Sequence) ) , 0 , Max(Sequence)) as max_sequence, IF(ISNULL( Min(Sequence) ) , 0 , Min(Sequence)) as min_sequence",false);
        $this->db->limit(1);
        $query = $this->db->get('slider');

        return $query->row_array();
    }

    // Change sequence of slider
    public function update_slider_sequence( $id, $type, $update_array )
    {
        foreach($update_array as $val){
            $val_arr = explode(':', $val);
            $this->db->update("slider", array( "Sequence" => $val_arr[1] ), array("Id" => $val_arr[0] ) );
        }
        /*$this->db->select("Id, Sequence");
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
        $this->db->update("slider", array( "Sequence" => $row_new['Sequence'] ), array("Id" => $row['Id'] ) );*/
    }
    
    public function get_sider_details($id) {
        $this->db->from('slider');
        $this->db->where('Id', $id);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }
    
    public function update_slider_image($id, $data) {

        $this->db->where('Id', $id);
        $this->db->update('slider', $data);
    }
}

?>