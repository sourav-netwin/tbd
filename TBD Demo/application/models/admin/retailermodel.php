<?php

/*
 * Author: Name:PM
 * Purpose: Model for controlling database interactions regarding the content.
 * Date:02-09-2015
 * Dependency: None
 */

class Retailermodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 26-08-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    public function get_retailers() {
        $this->db->select('retailers.Id,retailers.CompanyName,retailers.LogoImage,retailers.IsActive');

        $this->db->where(
                array(
                    'retailers.IsActive' => 1,
                    'retailers.IsRemoved' => 0
        ));
        $this->db->order_by("CompanyName");
        $query = $this->db->get('retailers');

        return $query->result_array();
    }

    public function get_retailer_details($retailer_id) {
        $this->db->select('retailers.*, users.FirstName, users.LastName');
        $this->db->from('retailers');
        $this->db->join('users', 'users.Id = retailers.RetailerAdminId','left');
        $this->db->where('retailers.Id', $retailer_id);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function update_retailer($retailer_id,$data) {

        $this->db->where('Id', $retailer_id);
        $this->db->update('retailers', $data);
    }

    public function add_retailer( $data ) {

        $this->db->insert('retailers', $data);
        return $this->db->insert_id();
    }

    public function delete_retailer($retailer_id) {

        $retailer_data = $this->get_retailer_details( $retailer_id );

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $retailer_data['RetailerAdminId']);
        $this->db->update('users', $data);

        $data = array('IsRemoved' => "1");
        $this->db->where('Id', $retailer_id);
        $this->db->update('retailers', $data);

        return TRUE;
    }

    public function change_status($retailer_id, $status) {

        $retailer_data = $this->get_retailer_details( $retailer_id );

        $data = array('IsActive' => $status);
        $this->db->where('Id', $retailer_data['RetailerAdminId']);
        $this->db->update('users', $data);

        $data = array('IsActive' => $status);
        $this->db->where('Id', $retailer_id);
        $this->db->update('retailers', $data);

        return TRUE;
    }

    public function get_retailer_by_name( $company_name )
    {
        $this->db->select('Id');
        $this->db->from('retailers');
        $this->db->where('CompanyName', $company_name);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    public function get_retailer_categories( $retailer_id )
    {
        $retailercategories = array();
        $this->db->select('CategoryID');
        $this->db->from('retailercategories');
        $this->db->where('RetailerId', $retailer_id);
        $query = $this->db->get();

        foreach ($query->result_array() as $res) {
            array_push( $retailercategories, $res['CategoryID'] );
        }

        return $retailercategories;
    }

    public function delete_assigned_categories( $retailer_id )
    {
        $this->db->delete("retailercategories", array("RetailerId" => $retailer_id));
    }

    public function assign_category( $data )
    {
        $this->db->insert('retailercategories', $data);
        return $this->db->insert_id();
    }

    public function get_category_count( $retailer_id )
    {
        $this->db->select('COUNT(CategoryID) as cat_cnt');
        $this->db->from('retailercategories');
        $this->db->where( array( 'RetailerId' => $retailer_id, 'IsActive' => 1 ) );
        $query = $this->db->get();

        return $query->row()->cat_cnt;
    }

    public function get_store_count( $retailer_id )
    {
        $this->db->select('COUNT(Id) as store_cnt');
        $this->db->from('storestypes');
        $this->db->where( array( 'RetailerId' => $retailer_id, 'IsActive' => 1, 'IsRemoved' => 0 ) );
        $query = $this->db->get();

        return $query->row()->store_cnt;
    }
    
    /*
     * Function to get store groups for the particular retailer
     */
    public function get_retailers_storegroups($retailerId) {
        $this->db->select('StoreGroupId');

        $this->db->where(
                array(
                    'RetailerId' => $retailerId
        ));
        $query = $this->db->get('retailers_storegroups');
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
    
    /* 
     * Function to remove groups  set new groups
     */
    public function set_storeGroups($retailerId, $groupIds) {          
        if($groupIds)
        {
            # Delete previous store groups
            $this -> db -> where('RetailerId', $retailerId);        
            $this -> db -> delete('retailers_storegroups');
        
            # Insert store Groups 
            foreach ($groupIds as $groupId) {
                $data = array(                
                    'RetailerId' => $retailerId,                
                    'StoreGroupId' => $groupId
                );

                $this -> db -> insert('retailers_storegroups', $data);
                $ids =  $this -> db -> insert_id();
            }
        
        }
    }
    
    
    
}
?>