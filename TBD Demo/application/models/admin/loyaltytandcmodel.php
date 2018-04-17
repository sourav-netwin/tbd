<?php

/*
 * Author: Name:MK
 * Purpose: Model for controlling special terms and conditions
 * Date:02-03-2017
 * Dependency: None
 */

class Loyaltytandcmodel extends CI_Model {

    private $table;

    public function __construct() {
        parent::__construct();

        $this -> table = 'loyalty_terms';
    }

    public function insert_terms($data) {
        if ($this -> db -> insert($this -> table, $data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function get_tandc_details($id) {
        $this -> db -> select('*')
            -> from('loyalty_terms')
            -> where(array(
                'Id' => $id,
                'IsActive' => 1,
                'IsRemoved' => 0
            ))
            -> limit(1);

        $query = $this -> db -> get();
        if($query -> num_rows() == 1) {
            return $query -> row_array();
        }
        else{
            return FALSE;
        }
    }
    
    public function update_terms($data,$id){
        $this -> db -> where('Id', $id);
        if($this -> db -> update($this -> table, $data)){
            return TRUE;
        }
        return FALSE;
    }
}