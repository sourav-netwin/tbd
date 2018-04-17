<?php

/*
 * Author: Name:PHN
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:04-09-2015
 * Dependency: None
 */

class Storeformatmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 04-09-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_store_formats($retailer_id = '') {
        $this->db->select('Id,StoreType');
        $this->db->from('storestypes');
        if($retailer_id != ''){
            $this->db->where('RetailerId', $retailer_id);
        }
//        $this->db->where('IsActive', 1);
        $this->db->where('IsRemoved', 0);
        $this -> db -> order_by('StoreType');
        $query = $this->db->get();        
        return $query->result_array();
    }

    public function update_store_format($store_format_id, $data) {

        $this->db->where('Id', $store_format_id);
        $this->db->update('storestypes', $data);
    }

    public function add_store_format($data) {

        $this->db->insert('storestypes', $data);
        return $this->db->insert_id();
    }

    public function delete_store_format($store_format_id) {

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $store_format_id);
        $this->db->update('storestypes', $data);

        return TRUE;
    }

    public function change_status($store_format_id, $status) {

        $data = array('IsActive' => $status);
        $this->db->where('Id', $store_format_id);
        $this->db->update('storestypes', $data);

        return TRUE;
    }

    public function get_storeformat_details($id) {
        $this->db->select('storestypes.*');
        $this->db->from('storestypes');
        $this->db->where('storestypes.Id', $id);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function update_user_count($id) {
        $this->db->set('UserCount', '`UserCount`+1', FALSE);
        $this->db->where('id', $id);
        $this->db->update('storestypes');
    }

     public function delete_storeformat_user($user_id,$store_id) {

        $data = array('Id' =>$user_id ,'StoreTypeId'=>$store_id);

        $this->db->delete('storeadmin', $data);

        $this->db->set('UserCount', '`UserCount`-1', FALSE);
        $this->db->where('id', $store_id);
        $this->db->update('storestypes');
    }
    
    
    /* 
     * Function to remove groups  and set new groups
     */
    public function set_storeGroups($storeFormatId, $groupIds) {          
        if($groupIds)
        {
            # Delete previous store groups
            $this -> db -> where('StoreFormatId', $storeFormatId);        
            $this -> db -> delete('storeformats_storegroups');
        
            # Insert store Groups 
            foreach ($groupIds as $groupId) {
                $data = array(                
                    'StoreFormatId' => $storeFormatId,                
                    'StoreGroupId' => $groupId
                );

                $this -> db -> insert('storeformats_storegroups', $data);
                $ids =  $this -> db -> insert_id();
            }
        
        }
    }
    
    
    /*
     * Function to get store groups for the particular store
     */
    public function get_storeformat_storegroups($storeFormatId) {
        $this->db->select('StoreGroupId');

        $this->db->where(
                array(
                    'StoreFormatId' => $storeFormatId
        ));
        $query = $this->db->get('storeformats_storegroups');
        //echo $this -> db -> last_query();die;
        
        if ($query->num_rows() > 0) {
            $results =  $query->result_array();
            $storeGroupIds = array();
            foreach ($results as $result)
            {
                $storeGroupIds[]=$result['StoreGroupId'];
            }
            return $storeGroupIds;
            
        } else {
            return FALSE;
        }
        
    }
    


}

?>